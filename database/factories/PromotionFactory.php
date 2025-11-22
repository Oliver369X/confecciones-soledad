<?php

namespace Database\Factories;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionFactory extends Factory
{
    protected $model = Promotion::class;

    public function definition(): array
    {
        $tipoDescuento = $this->faker->randomElement(['PORCENTAJE', 'MONTO_FIJO']);
        
        return [
            'codigo' => strtoupper($this->faker->unique()->lexify('??????')),
            'descripcion' => $this->faker->sentence(),
            'tipo_descuento' => $tipoDescuento,
            'valor_descuento' => $tipoDescuento === 'PORCENTAJE' 
                ? $this->faker->numberBetween(5, 30) 
                : $this->faker->numberBetween(10, 100),
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addMonth(),
            'activa' => true,
        ];
    }
}
