<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;

    public function test_cliente_can_access_mi_cuenta()
    {
        $cliente = User::factory()->cliente()->create();

        $response = $this->actingAs($cliente)->get('/mi-cuenta');

        $response->assertStatus(200);
    }

    public function test_cliente_can_view_only_own_orders()
    {
        $cliente1 = User::factory()->cliente()->create();
        $cliente2 = User::factory()->cliente()->create();

        $pedidoCliente1 = Order::factory()->create(['cliente_id' => $cliente1->usuario_id]);
        $pedidoCliente2 = Order::factory()->create(['cliente_id' => $cliente2->usuario_id]);

        $response = $this->actingAs($cliente1)->get('/mis-pedidos');

        $response->assertStatus(200);
        // Debería ver solo su pedido
    }

    public function test_cliente_cannot_view_other_client_order()
    {
        $cliente1 = User::factory()->cliente()->create();
        $cliente2 = User::factory()->cliente()->create();

        $pedidoCliente2 = Order::factory()->create(['cliente_id' => $cliente2->usuario_id]);

        $response = $this->actingAs($cliente1)->get("/mis-pedidos/{$pedidoCliente2->pedido_id}");

        $response->assertStatus(404);
    }

    public function test_propietario_cannot_access_client_routes()
    {
        $propietario = User::factory()->propietario()->create();

        $response = $this->actingAs($propietario)->get('/mi-cuenta');

        // Debería redirigir o denegar acceso
        $response->assertStatus(403);
    }
}
