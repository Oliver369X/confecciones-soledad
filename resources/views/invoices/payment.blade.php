<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo de Pago</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
            font-size: 14px;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #6366F1;
            padding-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #6366F1;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .subtitle {
            font-size: 12px;
            color: #666;
        }
        .invoice-info {
            float: right;
            text-align: right;
        }
        .client-info {
            float: left;
        }
        .clear {
            clear: both;
        }
        .section-title {
            background-color: #f3f4f6;
            padding: 8px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-left: 4px solid #6366F1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        th {
            background-color: #f9fafb;
            font-weight: bold;
        }
        .total-row td {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #333;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .status-paid {
            color: green;
            font-weight: bold;
            border: 1px solid green;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Confecciones Soledad</div>
        <div class="subtitle">Alta Costura y Diseño Exclusivo</div>
        <div>Santa Cruz, Bolivia | Tel: 75123456</div>
    </div>

    <div class="client-info">
        <strong>Cliente:</strong><br>
        {{ $payment->pedido->cliente->nombre_completo }}<br>
        {{ $payment->pedido->cliente->email }}<br>
        {{ $payment->pedido->cliente->telefono ?? 'Sin teléfono' }}
    </div>

    <div class="invoice-info">
        <strong>Recibo N°:</strong> {{ str_pad($payment->pago_id, 6, '0', STR_PAD_LEFT) }}<br>
        <strong>Fecha:</strong> {{ $payment->fecha_pago ? \Carbon\Carbon::parse($payment->fecha_pago)->format('d/m/Y H:i') : 'Pendiente' }}<br>
        <strong>Estado:</strong> <span class="status-paid">PAGADO</span>
    </div>

    <div class="clear"></div>

    <div class="section-title">Detalle del Pago</div>
    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Método</th>
                <th>Referencia</th>
                <th style="text-align: right;">Monto</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    Pago de Pedido #{{ $payment->pedido_id }}<br>
                    <small>{{ $payment->pedido->descripcion_prenda }}</small>
                    @if($payment->numero_cuota)
                        <br><strong>Cuota N° {{ $payment->numero_cuota }}</strong>
                    @endif
                </td>
                <td>{{ $payment->metodo_pago }}</td>
                <td>{{ $payment->pagofacil_transaction_id ?? $payment->company_transaction_id ?? '-' }}</td>
                <td style="text-align: right;">Bs {{ number_format($payment->monto, 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">Total Pagado:</td>
                <td style="text-align: right;">Bs {{ number_format($payment->monto, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="section-title">Resumen del Pedido</div>
    <table>
        <tr>
            <td><strong>Presupuesto Total:</strong></td>
            <td style="text-align: right;">Bs {{ number_format($payment->pedido->presupuesto_total, 2) }}</td>
        </tr>
        @if($payment->pedido->monto_descuento > 0)
        <tr>
            <td><strong>Descuento Aplicado:</strong></td>
            <td style="text-align: right; color: green;">- Bs {{ number_format($payment->pedido->monto_descuento, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td><strong>Total Pagado a la Fecha:</strong></td>
            <td style="text-align: right;">Bs {{ number_format($payment->pedido->pagos()->where('qr_status', 'PAID')->sum('monto'), 2) }}</td>
        </tr>
        <tr>
            <td><strong>Saldo Pendiente:</strong></td>
            <td style="text-align: right; color: red;">Bs {{ number_format($payment->pedido->saldo_pendiente, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Gracias por su preferencia.</p>
        <p>Este documento es un comprobante de pago válido para Confecciones Soledad.</p>
    </div>
</body>
</html>
