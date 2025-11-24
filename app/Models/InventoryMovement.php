<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario';
    protected $primaryKey = 'movimiento_id';

    protected $fillable = [
        'item_id',
        'pedido_id',
        'tipo_movimiento',  // Changed from 'tipo'
        'cantidad',
        'costo_unitario_ingreso',
        'fecha_movimiento',  // Changed from 'fecha'
        'motivo',  // Added
        'usuario_id',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'costo_unitario_ingreso' => 'decimal:2',
        'fecha_movimiento' => 'datetime',  // Changed from 'fecha'
    ];


    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Order::class, 'pedido_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
