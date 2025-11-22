<?php

namespace Tests\Feature;

use App\Models\PortfolioItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_view_portfolio()
    {
        PortfolioItem::factory()->count(3)->create();

        $response = $this->get('/portfolio');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_portfolio_item()
    {
        $admin = User::factory()->propietario()->create();

        $response = $this->actingAs($admin)->post('/portfolio', [
            'titulo' => 'Arreglo de Vestido de Novia',
            'descripcion' => 'Modificaciones y ajustes',
            'imagen_url_principal' => 'https://example.com/img1.jpg',
            'imagen_url_antes' => 'https://example.com/antes.jpg',
            'imagen_url_despues' => 'https://example.com/despues.jpg',
            'publicado' => true,
        ]);

        $response->assertRedirect('/portfolio');
        $this->assertDatabaseHas('portafolio_items', [
            'titulo' => 'Arreglo de Vestido de Novia',
        ]);
    }

    public function test_admin_can_update_portfolio_item()
    {
        $admin = User::factory()->propietario()->create();
        $item = PortfolioItem::factory()->create();

        $response = $this->actingAs($admin)->put("/portfolio/{$item->portafolio_id}", [
            'titulo' => 'Título Actualizado',
            'descripcion' => $item->descripcion,
            'imagen_url_principal' => $item->imagen_url_principal,
            'publicado' => false,
        ]);

        $response->assertRedirect('/portfolio');
        $this->assertDatabaseHas('portafolio_items', [
            'portafolio_id' => $item->portafolio_id,
            'titulo' => 'Título Actualizado',
        ]);
    }

    public function test_admin_can_delete_portfolio_item()
    {
        $admin = User::factory()->propietario()->create();
        $item = PortfolioItem::factory()->create();

        $response = $this->actingAs($admin)->delete("/portfolio/{$item->portafolio_id}");

        $response->assertRedirect('/portfolio');
        $this->assertDatabaseMissing('portafolio_items', [
            'portafolio_id' => $item->portafolio_id,
        ]);
    }
}
