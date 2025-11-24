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
    public function index()
    {
        return Inertia::render('Payments/Index', [
            'payments' => Payment::with(['pedido.cliente'])->latest()->get(),
            'qr_data' => session('qr_data'),  // ✅ Pasar qr_data de la sesión
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
        $total_pagado = $pedido->pagos()->sum('monto');
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
        \Log::info('🔵 Iniciando generación de QR', ['pedido_id' => $request->pedido_id]);

        $request->validate([
            'pedido_id' => 'required|exists:pedidos,pedido_id',
        ]);

        $pedido = Order::with('cliente')->findOrFail($request->pedido_id);
        \Log::info('📦 Pedido encontrado', [
            'pedido_id' => $pedido->pedido_id,
            'cliente' => $pedido->cliente->nombre_completo,
            'total' => $pedido->presupuesto_total
        ]);
        
        // Calcular saldo pendiente
        $total_pagado = $pedido->pagos()->sum('monto');
        $total_a_pagar = $pedido->presupuesto_total - $pedido->monto_descuento;
        $saldo_pendiente = $total_a_pagar - $total_pagado;

        \Log::info('💰 Cálculo de saldo', [
            'total_a_pagar' => $total_a_pagar,
            'total_pagado' => $total_pagado,
            'saldo_pendiente' => $saldo_pendiente
        ]);

        if ($saldo_pendiente <= 0) {
            \Log::warning('⚠️ Saldo pendiente es 0 o negativo');
            return redirect()->back()->withErrors(['pedido_id' => 'Este pedido ya está completamente pagado.']);
        }

        try {
            // Generar ID de transacción único
            $companyTransactionId = 'CONF-' . $pedido->pedido_id . '-' . time();
            
            \Log::info('🔑 ID de transacción generado', ['company_transaction_id' => $companyTransactionId]);

            // Preparar datos para PagoFácil
            $qrData = [
                'paymentMethod' => 4, // QR Simple (según la documentación)
                'clientName' => $pedido->cliente->nombre_completo,
                'documentType' => 1, // CI
                'documentId' => '123456',
                'phoneNumber' => $pedido->cliente->telefono ?? '68947764',
                'email' => $pedido->cliente->email,
                'paymentNumber' => $companyTransactionId,
                'amount' => (float) $saldo_pendiente,
                'currency' => 2, // BOB
                'clientCode' => (string) $pedido->cliente_id,
                // ✅ URL de producción (sin HTTPS por excepción del profesor)
                'callbackUrl' => 'https://www.tecnoweb.org.bo/inf513/grupo07sa/payments/callback',
                
                'orderDetail' => [
                    [
                        'serial' => 1,
                        'product' => "Pedido #{$pedido->pedido_id} - {$pedido->descripcion_prenda}",
                        'quantity' => 1,
                        'price' => (float) $saldo_pendiente,
                        'discount' => 0,
                        'total' => (float) $saldo_pendiente,
                    ]
                ]
            ];

            \Log::info('📋 Datos preparados para PagoFácil', [
                'qr_data' => $qrData,
                'callback_url' => $qrData['callbackUrl']
            ]);


            // Generar QR a través del servicio
            \Log::info('🌐 Llamando a PagoFacilService...');
            $response = $this->pagoFacilService->generateQr($qrData);
            \Log::info('✅ Respuesta de PagoFácil recibida', ['response' => $response]);

            // Guardar el pago pendiente con el QR
            $payment = Payment::create([
                'pedido_id' => $pedido->pedido_id,
                'monto' => $saldo_pendiente,
                'metodo_pago' => 'QR',
                'fecha_pago' => now(),
                'confirmado_por_id' => Auth::id(),
                'pagofacil_transaction_id' => $response['transactionId'],
                'company_transaction_id' => $companyTransactionId,
                'qr_base64' => $response['qrBase64'],
                'qr_status' => 'PENDING',
                'qr_expiration' => $response['expirationDate'],
            ]);

            \Log::info('💾 Pago guardado en BD', ['pago_id' => $payment->pago_id]);

            // ✅ Retornar con Inertia para que el QR se muestre
            return redirect()->route('payments.index')->with([
                'success' => 'QR generado exitosamente.',
                'qr_data' => [
                    'qrBase64' => $response['qrBase64'],
                    'transactionId' => $response['transactionId'],
                    'expirationDate' => $response['expirationDate'],
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

            // ✅ ACTUALIZAR ESTADO DEL PEDIDO
            if ($payment->pedido) {
                // Calcular si el pedido está totalmente pagado
                $totalPagado = $payment->pedido->pagos()->sum('monto');
                $totalAPagar = $payment->pedido->presupuesto_total - $payment->pedido->monto_descuento;

                if ($totalPagado >= $totalAPagar) {
                    // Pedido completamente pagado → cambiar a EN_PROCESO
                    $payment->pedido->update(['estado_pedido' => 'EN_PROCESO']);
                    \Log::info('✅ Pedido actualizado a EN_PROCESO', ['pedido_id' => $payment->pedido_id]);
                }
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
