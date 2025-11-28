<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function download(Payment $payment)
    {
        // Cargar relaciones necesarias
        $payment->load(['pedido.cliente', 'pedido.promocion']);

        // Verificar acceso (solo el dueño o admin)
        $user = auth()->user();
        if ($user->rol === 'CLIENTE' && $payment->pedido->cliente_id !== $user->usuario_id) {
            abort(403);
        }

        $pdf = Pdf::loadView('invoices.payment', ['payment' => $payment]);
        
        // Configurar papel
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('recibo-soledad-' . $payment->pago_id . '.pdf');
    }
}
