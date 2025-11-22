<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'cliente_id' => User::factory(),
            'ayudante_id' => null,
            'tipo_servicio' => $this->faker->randomElement(['ARREGLO', 'CONFECCION']),
            'descripcion_prenda' => $this->faker->sentence(),
            'estado' => 'PENDIENTE_PRESUPUESTO',
            'fecha_solicitud' => now(),
            'fecha_entrega_estimada' => null,
            'fecha_entregado' => null,
            'presupuesto_total' => $this->faker->randomFloat(2, 50, 500),
            'costo_materiales_total' => 0,
            'promocion_id' => null,
            'monto_descuento' => 0,
        ];
    }

    public function entregado()
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'ENTREGADO',
            'fecha_entregado' => now(),
        ]);
    }
}
