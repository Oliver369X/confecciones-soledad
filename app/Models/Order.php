<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'pedidos';
    protected $primaryKey = 'pedido_id';

    protected $fillable = [
        'cliente_id',
        'ayudante_id',
        'tipo_servicio',
        'descripcion_prenda',
        'estado',
        'fecha_solicitud',
        'fecha_entrega_estimada',
        'fecha_entregado',
        'presupuesto_total',
        'costo_materiales_total',
        'promocion_id',
        'monto_descuento',
        'numero_cuotas',
        'cuotas_pagadas',
        'saldo_pendiente',
        'codigo_promocion',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_entrega_estimada' => 'date',
        'fecha_entregado' => 'datetime',
        'presupuesto_total' => 'decimal:2',
        'costo_materiales_total' => 'decimal:2',
        'monto_descuento' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function ayudante()
    {
        return $this->belongsTo(User::class, 'ayudante_id');
    }

    public function promocion()
    {
        return $this->belongsTo(Promotion::class, 'promocion_id');
    }

    public function pagos()
    {
        return $this->hasMany(Payment::class, 'pedido_id');
    }

    public function resena()
    {
        return $this->hasOne(Review::class, 'pedido_id');
    }

    public function portafolioItems()
    {
        return $this->hasMany(PortfolioItem::class, 'pedido_id');
    }
    
    public function movimientosInventario()
    {
        return $this->hasMany(InventoryMovement::class, 'pedido_id');
    }
}
