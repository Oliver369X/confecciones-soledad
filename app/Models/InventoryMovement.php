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
        'tipo',
        'cantidad',
        'costo_unitario_ingreso',
        'fecha',
        'usuario_id',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'costo_unitario_ingreso' => 'decimal:2',
        'fecha' => 'datetime',
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
