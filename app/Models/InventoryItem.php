<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $table = 'inventario_items';
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'nombre',
        'stock_actual',
        'unidad_medida',
        'costo_promedio_ponderado',
    ];

    protected $casts = [
        'stock_actual' => 'decimal:2',
        'costo_promedio_ponderado' => 'decimal:2',
    ];

    public function movimientos()
    {
        return $this->hasMany(InventoryMovement::class, 'item_id');
    }
}
