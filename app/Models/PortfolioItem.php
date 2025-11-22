<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioItem extends Model
{
    use HasFactory;

    protected $table = 'portafolio_items';
    protected $primaryKey = 'portafolio_id';

    protected $fillable = [
        'pedido_id',
        'titulo',
        'descripcion',
        'imagen_url_principal',
        'imagen_url_antes',
        'imagen_url_despues',
        'publicado',
    ];

    protected $casts = [
        'publicado' => 'boolean',
    ];

    public function pedido()
    {
        return $this->belongsTo(Order::class, 'pedido_id');
    }
}
