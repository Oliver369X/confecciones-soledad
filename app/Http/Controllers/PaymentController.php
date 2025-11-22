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
        $payments = Payment::with(['pedido.cliente', 'confirmadoPor'])->orderBy('fecha_pago', 'desc')->get();
        return Inertia::render('Payments/Index', [
            'payments' => $payments
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
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,pedido_id',
        ]);

        $pedido = Order::with('cliente')->findOrFail($request->pedido_id);
        
        // Calcular saldo pendiente
        $total_pagado = $pedido->pagos()->sum('monto');
        $total_a_pagar = $pedido->presupuesto_total - $pedido->monto_descuento;
        $saldo_pendiente = $total_a_pagar - $total_pagado;

        if ($saldo_pendiente <= 0) {
            return redirect()->back()->withErrors(['pedido_id' => 'Este pedido ya está completamente pagado.']);
        }

        try {
            // Generar ID de transacción único
            $companyTransactionId = 'CONF-' . $pedido->pedido_id . '-' . time();

            // Preparar datos para PagoFácil
            $qrData = [
                'paymentMethod' => 4, // QR Simple (según la documentación)
                'clientName' => $pedido->cliente->nombre_completo,
                'documentType' => 1, // CI
                'documentId' => '000000', // Placeholder - deberías tener este campo en el modelo User
                'phoneNumber' => $pedido->cliente->telefono ?? '00000000',
                'email' => $pedido->cliente->email,
                'paymentNumber' => $companyTransactionId,
                'amount' => (float) $saldo_pendiente,
                'currency' => 2, // BOB
                'clientCode' => (string) $pedido->cliente_id,
                'callbackUrl' => route('payments.callback'),
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

            // Generar QR a través del servicio
            $response = $this->pagoFacilService->generateQr($qrData);

            // Guardar el pago pendiente con el QR
            Payment::create([
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

            return redirect()->back()->with('success', 'QR generado exitosamente.')->with('qr_data', $response);

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al generar QR: ' . $e->getMessage()]);
        }
    }

    /**
     * Callback from PagoFácil (Webhook)
     */
    public function callback(Request $request)
    {
        // Validar la notificación
        $pedidoId = $request->input('PedidoID'); // Company Transaction ID
        $estado = $request->input('Estado');

        // Buscar el pago por company_transaction_id
        $payment = Payment::where('company_transaction_id', $pedidoId)->first();

        if (!$payment) {
            return response()->json(['error' => 1, 'message' => 'Pago no encontrado'], 404);
        }

        // Actualizar el estado del pago si fue exitoso
        if ($estado === 'Completado' || $estado === 'PAID') {
            $payment->update([
                'qr_status' => 'PAID',
                'fecha_pago' => now(),
            ]);

            // Aquí podrías agregar lógica adicional, como enviar un email al cliente
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
