<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Order;
use App\Models\User;
use App\Services\PagoFacilService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $pagoFacilService;

    public function __construct(PagoFacilService $pagoFacilService)
    {
        $this->pagoFacilService = $pagoFacilService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $pedidoId = session('current_pedido_id'); // ID del pedido actual del QR
        
        // Si es cliente, solo mostrar pagos de SU pedido actual (el que generó el QR)
        if ($user->rol === 'CLIENTE' && $pedidoId) {
            $payments = Payment::with(['pedido.cliente'])
                ->whereHas('pedido', function($q) use ($user, $pedidoId) {
                    $q->where('pedido_id', $pedidoId)
                      ->where('cliente_id', $user->usuario_id);
                })
                ->latest()
                ->get();
        } else {
            // Admins ven todos
            $payments = Payment::with(['pedido.cliente'])->latest()->get();
        }

        // ✅ RECUPERAR QR PENDIENTE SI EXISTE (Persistencia)
        $qrData = session('qr_data');
        if (!$qrData && $user->rol === 'CLIENTE' && $pedidoId) {
            $pendingPayment = Payment::where('pedido_id', $pedidoId)
                ->where('qr_status', 'PENDING')
                ->latest()
                ->first();
            
            if ($pendingPayment) {
                $qrData = [
                    'qrBase64' => $pendingPayment->qr_base64,
                    'transactionId' => $pendingPayment->pagofacil_transaction_id ?? $pendingPayment->company_transaction_id,
                    'expirationDate' => $pendingPayment->qr_expiration,
                    'monto' => $pendingPayment->monto,
                    'cuota' => $pendingPayment->numero_cuota,
                    'payment_id' => $pendingPayment->pago_id,
                    'pedido_id' => $pendingPayment->pedido_id
                ];
            }
        }
        
        return Inertia::render('Payments/Index', [
            'payments' => $payments,
            'qr_data' => $qrData,
            'current_pedido_id' => $pedidoId,
            'auth' => [
                'user' => $user
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage (Manual Payment)
     */
    public function store(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,pedido_id',
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:EFECTIVO,TRANSFERENCIA,QR',
            'comprobante_url' => 'nullable|string',
        ]);

        $pedido = Order::findOrFail($request->pedido_id);
        
        // Verificar que el monto no exceda el saldo pendiente
        $total_pagado = $pedido->pagos()->where('qr_status', 'PAID')->sum('monto');
        $total_a_pagar = $pedido->presupuesto_total - $pedido->monto_descuento;
        $saldo_pendiente = $total_a_pagar - $total_pagado;

        if ($request->monto > $saldo_pendiente) {
            return redirect()->back()->withErrors(['monto' => 'El monto excede el saldo pendiente.']);
        }

        Payment::create([
            'pedido_id' => $request->pedido_id,
            'monto' => $request->monto,
            'metodo_pago' => $request->metodo_pago,
            'fecha_pago' => now(),
            'comprobante_url' => $request->comprobante_url,
            'confirmado_por_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Pago registrado exitosamente.');
    }

    /**
     * Generate QR for payment
     */
    public function generateQr(Request $request)
    {
        // \Log::info('🔵 Iniciando generación de QR', ['pedido_id' => $request->pedido_id]);

        $request->validate([
            'pedido_id' => 'required|exists:pedidos,pedido_id',
        ]);

        $pedido = Order::with('cliente')->findOrFail($request->pedido_id);
        
        // 🚫 BLOQUEAR SI YA HAY UN PAGO PENDIENTE
        $pagoPendiente = $pedido->pagos()->where('qr_status', 'PENDING')->first();
        if ($pagoPendiente) {
             // Guardar ID en sesión para que el index lo muestre
             session(['current_pedido_id' => $pedido->pedido_id]);
             
             return redirect()->route('payments.index')->withErrors([
                 'error' => 'Ya tienes un pago pendiente. Por favor completa o cancela el anterior.'
             ]);
        }

        // \Log::info('📦 Pedido encontrado', [
        //     'pedido_id' => $pedido->pedido_id,
        //     'cliente' => $pedido->cliente->nombre_completo,
        //     'total' => $pedido->presupuesto_total
        // ]);
        
        // \Log::info('🔍 DEBUG - Información del Pedido:', [
        //     'pedido_id' => $pedido->pedido_id,
        //     'presupuesto_total' => $pedido->presupuesto_total,
        //     'monto_descuento' => $pedido->monto_descuento,
        //     'estado' => $pedido->estado,
        //     'numero_cuotas' => $pedido->numero_cuotas,
        //     'cuotas_pagadas' => $pedido->cuotas_pagadas,
        //     'saldo_pendiente_db' => $pedido->saldo_pendiente,
        // ]);

        // Verificar que el pedido tenga un presupuesto definido
        if (!$pedido->presupuesto_total || $pedido->presupuesto_total <= 0) {
            \Log::warning('⚠️ Pedido sin presupuesto definido', ['pedido_id' => $pedido->pedido_id]);
            return redirect()->back()->withErrors(['pedido_id' => 'Este pedido aún no tiene presupuesto definido. El administrador debe asignarlo primero.']);
        }

        // Verificar que el estado del pedido permita pagos
        $estadosPermitidos = ['CONFIRMADO', 'EN_PROCESO', 'LISTO_ENTREGAR'];
        if (!in_array($pedido->estado, $estadosPermitidos)) {
            \Log::warning('⚠️ Estado no permite pagos', [
                'pedido_id' => $pedido->pedido_id, 
                'estado' => $pedido->estado
            ]);
            return redirect()->back()->withErrors(['pedido_id' => 'Este pedido debe estar en estado CONFIRMADO para poder generar un pago. Estado actual: ' . $pedido->estado]);
        }
        
        // Obtener todos los pagos para debugging
        $todosPagos = $pedido->pagos()->get();
        // \Log::info('🔍 DEBUG - Todos los Pagos:', [
        //     'total_pagos' => $todosPagos->count(),
        //     'pagos' => $todosPagos->map(function($p) {
        //         return [
        //             'pago_id' => $p->pago_id,
        //             'monto' => $p->monto,
        //             'qr_status' => $p->qr_status,
        //             'metodo_pago' => $p->metodo_pago,
        //         ];
        //     })->toArray()
        // ]);

        // Calcular saldo pendiente (Usando la BD si está actualizado, o recalculando)
        $total_pagado = $pedido->pagos()->where('qr_status', 'PAID')->sum('monto'); // Solo pagados
        $total_a_pagar = $pedido->presupuesto_total - $pedido->monto_descuento;
        $saldo_pendiente = $total_a_pagar - $total_pagado;

        // \Log::info('💰 Cálculo de saldo DETALLADO', [
        //     'presupuesto_total' => $pedido->presupuesto_total,
        //     'monto_descuento' => $pedido->monto_descuento,
        //     'total_a_pagar' => $total_a_pagar,
        //     'total_pagado' => $total_pagado,
        //     'saldo_pendiente' => $saldo_pendiente,
        //     'saldo_pendiente <= 0.5?' => ($saldo_pendiente <= 0.5)
        // ]);

        // Solo bloquear si hay pagos Y el saldo es menor a 1 centavo
        if ($total_pagado > 0 && $saldo_pendiente < 0.01) {
            \Log::error('❌ BLOQUEADO - Saldo pendiente es 0 o negativo', [
                'saldo_pendiente' => $saldo_pendiente,
                'total_a_pagar' => $total_a_pagar,
                'total_pagado' => $total_pagado
            ]);
            return redirect()->back()->withErrors(['error' => 'Este pedido ya está completamente pagado. Saldo: Bs ' . number_format($saldo_pendiente, 2)]);
        }

        // Lógica de Cuotas
        $monto_a_cobrar = $saldo_pendiente;
        $numero_cuota_actual = null;

        if ($pedido->numero_cuotas > 1) {
            $monto_cuota = $total_a_pagar / $pedido->numero_cuotas;
            // Si es la última cuota, cobramos lo que falte para cerrar
            if ($pedido->cuotas_pagadas >= ($pedido->numero_cuotas - 1)) {
                $monto_a_cobrar = $saldo_pendiente;
            } else {
                $monto_a_cobrar = $monto_cuota;
            }
            $numero_cuota_actual = $pedido->cuotas_pagadas + 1;
        }

        try {
            // Generar ID de transacción único
            $companyTransactionId = 'CONF-' . $pedido->pedido_id . '-' . time();
            
            // \Log::info('🔑 ID de transacción generado', ['company_transaction_id' => $companyTransactionId]);

            // Preparar datos para PagoFácil
            $qrData = [
                'paymentMethod' => 4, // QR Simple
                'clientName' => $pedido->cliente->nombre_completo,
                'documentType' => 1, // CI
                'documentId' => '123456',
                'phoneNumber' => $pedido->cliente->telefono ?? '68947764',
                'email' => $pedido->cliente->email,
                'paymentNumber' => $companyTransactionId,
                'amount' => (float) $monto_a_cobrar, // Usar monto de la cuota
                'currency' => 2, // BOB
                'clientCode' => (string) $pedido->cliente_id,
                'callbackUrl' => 'https://www.tecnoweb.org.bo/inf513/grupo07sa/payments/callback',
                
                'orderDetail' => [
                    [
                        'serial' => 1,
                        'product' => "Pedido #{$pedido->pedido_id} - Cuota " . ($numero_cuota_actual ?? 'Única'),
                        'quantity' => 1,
                        'price' => (float) $monto_a_cobrar,
                        'discount' => 0,
                        'total' => (float) $monto_a_cobrar,
                    ]
                ]
            ];

            // \Log::info('📋 Datos preparados para PagoFácil', [
            //     'qr_data' => $qrData,
            //     'callback_url' => $qrData['callbackUrl']
            // ]);


            // Generar QR a través del servicio
            // \Log::info('🌐 Llamando a PagoFacilService...');
            $response = $this->pagoFacilService->generateQr($qrData);
            // \Log::info('✅ Respuesta de PagoFácil recibida', ['response' => $response]);

            // Guardar el pago pendiente con el QR
            $payment = Payment::create([
                'pedido_id' => $pedido->pedido_id,
                'monto' => $monto_a_cobrar,
                'metodo_pago' => 'QR',
                'fecha_pago' => now(),
                'confirmado_por_id' => Auth::id(),
                'pagofacil_transaction_id' => $response['transactionId'],
                'company_transaction_id' => $companyTransactionId,
                'qr_base64' => $response['qrBase64'],
                'qr_status' => 'PENDING',
                'qr_expiration' => $response['expirationDate'],
                'numero_cuota' => $numero_cuota_actual, // Guardar número de cuota
            ]);

            // \Log::info('💾 Pago guardado en BD', ['pago_id' => $payment->pago_id]);

            // ✅ Guardar el ID del pedido en sesión para filtrar pagos
            session(['current_pedido_id' => $pedido->pedido_id]);

            // ✅ Retornar con Inertia para que el QR se muestre
            return redirect()->route('payments.index')->with([
                'success' => 'QR generado exitosamente.',
                'qr_data' => [
                    'qrBase64' => $response['qrBase64'],
                    'transactionId' => $response['transactionId'],
                    'expirationDate' => $response['expirationDate'],
                    'monto' => $monto_a_cobrar,
                    'cuota' => $numero_cuota_actual,
                    'payment_id' => $payment->pago_id, // Para auto-confirmación
                    'pedido_id' => $pedido->pedido_id
                ]
            ]);


        } catch (\Exception $e) {
            \Log::error('❌ Error al generar QR2', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withErrors(['error' => 'Error al generar QR 1: ' . $e->getMessage()]);
        }
    }

    /**
     * Callback from PagoFácil (Webhook)
     */
    public function callback(Request $request)
    {
        \Log::info('📞 Callback recibido de PagoFácil', $request->all());

        // Validar la notificación
        $pedidoId = $request->input('PedidoID'); // Company Transaction ID
        $estado = $request->input('Estado');

        // Buscar el pago por company_transaction_id
        $payment = Payment::where('company_transaction_id', $pedidoId)->first();

        if (!$payment) {
            \Log::warning('⚠️ Pago no encontrado', ['company_transaction_id' => $pedidoId]);
            return response()->json(['error' => 1, 'message' => 'Pago no encontrado'], 404);
        }

        // Actualizar el estado del pago si fue exitoso
        if ($estado === 'Completado' || $estado === 'PAID' || $estado === 2) {  // 2 = PAID según API
            $payment->update([
                'qr_status' => 'PAID',
                'fecha_pago' => now(),
            ]);

            // ✅ ACTUALIZAR ESTADO DEL PEDIDO Y CUOTAS
            if ($payment->pedido) {
                $pedido = $payment->pedido;
                
                // Actualizar cuotas pagadas
                if ($payment->numero_cuota) {
                    $pedido->cuotas_pagadas = $payment->numero_cuota;
                }

                // Recalcular saldo
                $totalPagado = $pedido->pagos()->where('qr_status', 'PAID')->sum('monto');
                $totalAPagar = $pedido->presupuesto_total - $pedido->monto_descuento;
                $saldo = max(0, $totalAPagar - $totalPagado);
                
                $pedido->saldo_pendiente = $saldo;

                if ($saldo <= 0.5) {
                    // Pedido completamente pagado → cambiar a EN_PROCESO
                    $pedido->estado = 'EN_PROCESO';
                    \Log::info('✅ Pedido actualizado a EN_PROCESO', ['pedido_id' => $pedido->pedido_id]);
                }
                
                $pedido->save();
            }

            \Log::info('✅ Pago confirmado', ['pago_id' => $payment->pago_id]);
        }

        // Responder conforme a la API de PagoFácil
        return response()->json([
            'error' => 0,
            'status' => 1,
            'message' => 'Notificación recibida correctamente',
            'values' => true
        ], 200);
    }

    /**
     * Consultar estado de transacción (MANUAL)
     */
    public function checkTransactionStatus($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);

        if (!$payment->pagofacil_transaction_id) {
            return response()->json(['error' => 'Este pago no tiene un ID de transacción de PagoFácil'], 400);
        }

        try {
            \Log::info('🔍 Consultando estado de transacción', [
                'payment_id' => $paymentId,
                'transaction_id' => $payment->pagofacil_transaction_id
            ]);

            // Consultar estado en PagoFácil
            $result = $this->pagoFacilService->consultarTransaccion($payment->pagofacil_transaction_id);

            \Log::info('📊 Resultado de consulta', $result);

            // Actualizar estado según respuesta
            if (isset($result['paymentStatus'])) {
                $status = $result['paymentStatus'];
                
                if ($status == 2) {  // PAID
                    $payment->update(['qr_status' => 'PAID']);

                    // Actualizar pedido si está completamente pagado
                    if ($payment->pedido) {
                        $totalPagado = $payment->pedido->pagos()->sum('monto');
                        $totalAPagar = $payment->pedido->presupuesto_total - $payment->pedido->monto_descuento;

                        if ($totalPagado >= $totalAPagar) {
                            $payment->pedido->update(['estado_pedido' => 'EN_PROCESO']);
                        }
                    }

                    return response()->json([
                        'success' => true,
                        'status' => 'PAID',
                        'message' => '✅ Pago confirmado exitosamente',
                        'data' => $result
                    ]);
                } elseif ($status == 1) {  // PENDING
                    return response()->json([
                        'success' => true,
                        'status' => 'PENDING',
                        'message' => '⏳ Pago pendiente de confirmación',
                        'data' => $result
                    ]);
                } elseif ($status == 3) {  // CANCELLED
                    $payment->update(['qr_status' => 'CANCELLED']);
                    return response()->json([
                        'success' => true,
                        'status' => 'CANCELLED',
                        'message' => '❌ Pago cancelado',
                        'data' => $result
                    ]);
                } elseif ($status == 4) {  // EXPIRED
                    $payment->update(['qr_status' => 'EXPIRED']);
                    return response()->json([
                        'success' => true,
                        'status' => 'EXPIRED',
                        'message' => '⏰ QR expirado',
                        'data' => $result
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Estado consultado',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ Error al consultar estado', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al consultar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PLAN B: Confirmar pago manualmente (simulación cuando el callback no llega)
     * Este método se llama desde el frontend después de 30 segundos
     */
    public function simulateConfirmation($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        
        // Solo permitir si es el dueño del pago o admin
        $user = Auth::user();
        if ($user->rol === 'CLIENTE' && $payment->pedido->cliente_id !== $user->usuario_id) {
            abort(403);
        }

        // Solo confirmar si está PENDING
        if ($payment->qr_status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => 'Este pago ya fue procesado'
            ]);
        }

        // \Log::info('🔄 PLAN B: Confirmando pago manualmente', [
        //     'payment_id' => $paymentId,
        //     'user' => $user->nombre_completo
        // ]);

        // Actualizar el pago a PAID
        $payment->update([
            'qr_status' => 'PAID',
            'fecha_pago' => now(),
        ]);

        // Actualizar el pedido
        $pedido = $payment->pedido;
        
        // Si tiene cuotas, incrementar cuotas_pagadas
        if ($payment->numero_cuota) {
            $pedido->cuotas_pagadas = $payment->numero_cuota;
        }

        // Recalcular saldo pendiente
        $totalPagado = $pedido->pagos()->where('qr_status', 'PAID')->sum('monto');
        $totalAPagar = $pedido->presupuesto_total - $pedido->monto_descuento;
        $saldoPendiente = max(0, $totalAPagar - $totalPagado);
        
        $pedido->saldo_pendiente = $saldoPendiente;

        // Si está completamente pagado, cambiar estado
        if ($saldoPendiente <= 0.01) {
            $pedido->estado = 'EN_PROCESO';
        }

        $pedido->save();

        // \Log::info('✅ Pago confirmado manualmente', [
        //     'payment_id' => $paymentId,
        //     'pedido_id' => $pedido->pedido_id,
        //     'nuevo_estado' => $pedido->estado,
        //     'saldo_pendiente' => $saldoPendiente
        // ]);

        return response()->json([
            'success' => true,
            'message' => '✅ Pago confirmado exitosamente',
            'payment' => $payment->fresh(),
            'pedido_updated' => [
                'estado' => $pedido->estado,
                'saldo_pendiente' => $saldoPendiente,
                'cuotas_pagadas' => $pedido->cuotas_pagadas
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        if (Auth::user()->rol !== 'PROPIETARIO') {
            abort(403);
        }

        $payment->delete();
        return redirect()->back()->with('success', 'Pago eliminado.');
    }
}
