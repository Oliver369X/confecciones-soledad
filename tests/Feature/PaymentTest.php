<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_payments()
    {
        $propietario = User::factory()->propietario()->create();
        $order = Order::factory()->create();
        Payment::factory()->count(3)->create(['pedido_id' => $order->pedido_id]);

        $response = $this->actingAs($propietario)->get('/payments');

        $response->assertStatus(200);
    }

    public function test_owner_can_register_payment()
    {
        $propietario = User::factory()->propietario()->create();
        $order = Order::factory()->create(['presupuesto_total' => 200]);

        $response = $this->actingAs($propietario)->post('/payments', [
            'pedido_id' => $order->pedido_id,
            'monto' => 100.00,
            'metodo_pago' => 'EFECTIVO',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pagos', [
            'pedido_id' => $order->pedido_id,
            'monto' => 100.00,
        ]);
    }

    public function test_cannot_pay_more_than_pending_balance()
    {
        $propietario = User::factory()->propietario()->create();
        $order = Order::factory()->create(['presupuesto_total' => 100]);

        Payment::factory()->create([
            'pedido_id' => $order->pedido_id,
            'monto' => 80,
        ]);

        $response = $this->actingAs($propietario)->post('/payments', [
            'pedido_id' => $order->pedido_id,
            'monto' => 50.00, // Solo queda saldo de 20
            'metodo_pago' => 'EFECTIVO',
        ]);

        $response->assertSessionHasErrors('monto');
    }
}
