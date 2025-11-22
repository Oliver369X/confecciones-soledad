<?php

namespace Database\Factories;

use App\Models\InventoryMovement;
use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryMovementFactory extends Factory
{
    protected $model = InventoryMovement::class;

    public function definition(): array
    {
        return [
            'item_id' => InventoryItem::factory(),
            'usuario_id' => User::factory(),
            'pedido_id' => null,
            'tipo_movimiento' => $this->faker->randomElement(['ENTRADA', 'SALIDA']),
            'cantidad' => $this->faker->randomFloat(2, 1, 50),
            'fecha_movimiento' => now(),
            'motivo' => $this->faker->sentence(),
        ];
    }
}
