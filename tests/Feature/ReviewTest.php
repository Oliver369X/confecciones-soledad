<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_view_reviews()
    {
        Review::factory()->count(3)->create();

        $response = $this->get('/reviews');

        $response->assertStatus(200);
    }

    public function test_client_can_review_delivered_order()
    {
        $cliente = User::factory()->cliente()->create();
        $order = Order::factory()->create([
            'cliente_id' => $cliente->usuario_id,
            'estado' => 'ENTREGADO',
        ]);

        $response = $this->actingAs($cliente)->post('/reviews', [
            'pedido_id' => $order->pedido_id,
            'calificacion' => 5,
            'comentario' => 'Excelente servicio!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('resenas', [
            'pedido_id' => $order->pedido_id,
            'calificacion' => 5,
        ]);
    }

    public function test_client_cannot_review_pending_order()
    {
        $cliente = User::factory()->cliente()->create();
        $order = Order::factory()->create([
            'cliente_id' => $cliente->usuario_id,
            'estado' => 'PENDIENTE_PRESUPUESTO',
        ]);

        $response = $this->actingAs($cliente)->post('/reviews', [
            'pedido_id' => $order->pedido_id,
            'calificacion' => 5,
            'comentario' => 'Buen servicio',
        ]);

        $response->assertSessionHasErrors();
    }
}
