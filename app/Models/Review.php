<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'resenas';
    protected $primaryKey = 'resena_id';

    protected $fillable = [
        'pedido_id',
        'cliente_id',
        'calificacion',
        'comentario',
        'fecha',
        'publicada',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'publicada' => 'boolean',
    ];

    public function pedido()
    {
        return $this->belongsTo(Order::class, 'pedido_id');
    }

    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }
}
