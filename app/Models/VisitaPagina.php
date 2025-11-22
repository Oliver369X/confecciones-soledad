<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitaPagina extends Model
{
    protected $table = 'visitas_pagina';
    protected $primaryKey = 'visita_id';
    
    protected $fillable = [
        'pagina',
        'contador',
    ];

    /**
     * Incrementar contador de la página
     */
    public static function incrementar(string $pagina): int
    {
        $visita = self::firstOrCreate(
            ['pagina' => $pagina],
            ['contador' => 0]
        );

        $visita->increment('contador');
        
        return $visita->fresh()->contador;
    }

    /**
     * Obtener contador de una página
     */
    public static function obtenerContador(string $pagina): int
    {
        $visita = self::where('pagina', $pagina)->first();
        return $visita ? $visita->contador : 0;
    }
}
