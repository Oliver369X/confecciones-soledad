<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'pedido_id' => Order::factory(),
            'monto' => $this->faker->randomFloat(2, 10, 200),
            'metodo_pago' => $this->faker->randomElement(['EFECTIVO', 'TRANSFERENCIA', 'QR']),
            'fecha_pago' => now(),
            'confirmado_por_id' => User::factory(),
            'pagofacil_transaction_id' => null,
            'company_transaction_id' => null,
            'qr_base64' => null,
            'qr_status' => null,
            'qr_expiration' => null,
            'comprobante_url' => null,
        ];
    }
}
