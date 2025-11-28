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

        \Log::info('🔄 PLAN B: Confirmando pago manualmente', [
            'payment_id' => $paymentId,
            'user' => $user->nombre_completo
        ]);

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

        \Log::info('✅ Pago confirmado manualmente', [
            'payment_id' => $paymentId,
            'pedido_id' => $pedido->pedido_id,
            'nuevo_estado' => $pedido->estado,
            'saldo_pendiente' => $saldoPendiente
        ]);

        return response()->json([
            'success' => true,
            'message' => '✅ Pago confirmado exitosamente (simulación)',
            'payment' => $payment,
            'pedido_updated' => [
                'estado' => $pedido->estado,
                'saldo_pendiente' => $saldoPendiente,
                'cuotas_pagadas' => $pedido->cuotas_pagadas
            ]
        ]);
    }
