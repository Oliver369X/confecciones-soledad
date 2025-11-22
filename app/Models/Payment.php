<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'pagos';
    protected $primaryKey = 'pago_id';

    protected $fillable = [
        'pedido_id',
        'monto',
        'metodo_pago',
        'fecha_pago',
        'confirmado_por_id',
        'pagofacil_transaction_id',
        'company_transaction_id',
        'qr_base64',
        'qr_status',
        'qr_expiration',
        'comprobante_url',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'datetime',
    ];

    public function pedido()
    {
        return $this->belongsTo(Order::class, 'pedido_id');
    }

    public function confirmadoPor()
    {
        return $this->belongsTo(User::class, 'confirmado_por_id');
    }
}
