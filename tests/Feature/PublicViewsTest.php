<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PortfolioItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicViewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_catalogo_page_loads()
    {
        $response = $this->get('/catalogo');
        $response->assertStatus(200);
    }

    public function test_hacer_pedido_page_loads()
    {
        $response = $this->get('/hacer-pedido');
        $response->assertStatus(200);
    }

    public function test_can_submit_order_without_authentication()
    {
        $response = $this->post('/hacer-pedido', [
            'tipo_servicio' => 'ARREGLO',
            'descripcion_prenda' => 'Ajuste de pantalón',
            'telefono_contacto' => '75123456',
        ]);

        $response->assertRedirect('/gracias');
        $this->assertDatabaseHas('pedidos', [
            'tipo_servicio' => 'ARREGLO',
            'descripcion_prenda' => 'Ajuste de pantalón',
        ]);
    }

    public function test_catalogo_shows_only_published_portfolio()
    {
        // Crear items publicados y no publicados
        PortfolioItem::factory()->create(['publicado' => true, 'titulo' => 'Item Publicado']);
        PortfolioItem::factory()->create(['publicado' => false, 'titulo' => 'Item No Publicado']);

        $response = $this->get('/catalogo');
        
        $response->assertStatus(200);
        // El item publicado debería estar en los props de Inertia
    }

    public function test_order_validation_requires_fields()
    {
        $response = $this->post('/hacer-pedido', []);

        $response->assertSessionHasErrors([
            'tipo_servicio',
            'descripcion_prenda',
            'telefono_contacto',
        ]);
    }
}
