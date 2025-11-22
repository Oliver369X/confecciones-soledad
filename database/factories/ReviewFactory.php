<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'pedido_id' => Order::factory()->entregado(),
            'cliente_id' => User::factory(),
            'calificacion' => $this->faker->numberBetween(1, 5),
            'comentario' => $this->faker->paragraph(),
            'publicada' => true,
        ];
    }
}
