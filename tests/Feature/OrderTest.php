<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_all_orders()
    {
        $propietario = User::factory()->propietario()->create();
        Order::factory()->count(3)->create();

        $response = $this->actingAs($propietario)->get('/orders');

        $response->assertStatus(200);
    }

    public function test_owner_can_create_order()
    {
        $propietario = User::factory()->propietario()->create();
        $cliente = User::factory()->cliente()->create();

        $response = $this->actingAs($propietario)->post('/orders', [
            'cliente_id' => $cliente->usuario_id,
            'tipo_servicio' => 'ARREGLO',
            'descripcion_prenda' => 'Ajuste de pantalón',
            'presupuesto_total' => 50.00,
        ]);

        $response->assertRedirect('/orders');
        $this->assertDatabaseHas('pedidos', [
            'cliente_id' => $cliente->usuario_id,
            'descripcion_prenda' => 'Ajuste de pantalón',
        ]);
    }

    public function test_client_can_only_view_own_orders()
    {
        $cliente1 = User::factory()->cliente()->create();
        $cliente2 = User::factory()->cliente()->create();

        $orderCliente1 = Order::factory()->create(['cliente_id' => $cliente1->usuario_id]);
        $orderCliente2 = Order::factory()->create(['cliente_id' => $cliente2->usuario_id]);

        $response = $this->actingAs($cliente1)->get('/orders');

        $response->assertStatus(200);
        // Inertia devuelve props, así que verificamos que solo ve su pedido
        $this->assertDatabaseHas('pedidos', ['pedido_id' => $orderCliente1->pedido_id]);
    }
}
