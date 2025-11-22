<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryItemFactory extends Factory
{
    protected $model = InventoryItem::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->word() . ' ' . $this->faker->randomElement(['Tela', 'Hilo', 'Botón', 'Cierre']),
            'descripcion' => $this->faker->sentence(),
            'cantidad_stock' => $this->faker->randomFloat(2, 10, 200),
            'unidad_medida' => $this->faker->randomElement(['Metros', 'Unidades', 'Kilos']),
            'costo_unitario' => $this->faker->randomFloat(2, 5, 50),
        ];
    }
}
