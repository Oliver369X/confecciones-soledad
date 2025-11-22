<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $table = 'promociones';
    protected $primaryKey = 'promocion_id';

    protected $fillable = [
        'codigo',
        'descripcion',
        'tipo_descuento',
        'valor_descuento',
        'fecha_inicio',
        'fecha_fin',
        'activa',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activa' => 'boolean',
        'valor_descuento' => 'decimal:2',
    ];

    public function pedidos()
    {
        return $this->hasMany(Order::class, 'promocion_id');
    }
}
