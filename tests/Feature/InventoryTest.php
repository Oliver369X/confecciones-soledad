<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_inventory()
    {
        $propietario = User::factory()->propietario()->create();
        InventoryItem::factory()->count(3)->create();

        $response = $this->actingAs($propietario)->get('/inventory');

        $response->assertStatus(200);
    }

    public function test_owner_can_create_inventory_item()
    {
        $propietario = User::factory()->propietario()->create();

        $response = $this->actingAs($propietario)->post('/inventory', [
            'nombre_material' => 'Tela Algodón',
            'descripcion' => 'Tela blanca para camisas',
            'cantidad_stock' => 100,
            'unidad_medida' => 'Metros',
            'costo_unitario' => 15.50,
        ]);

        $response->assertRedirect('/inventory');
        $this->assertDatabaseHas('inventario_items', [
            'nombre_material' => 'Tela Algodón',
        ]);
    }

    public function test_owner_can_register_movement()
    {
        $propietario = User::factory()->propietario()->create();
        $item = InventoryItem::factory()->create([
            'cantidad_stock' => 100,
        ]);

        $response = $this->actingAs($propietario)->post("/inventory/{$item->item_id}/movement", [
            'tipo_movimiento' => 'SALIDA',
            'cantidad' => 10,
            'motivo' => 'Uso en pedido de prueba',
        ]);

        $response->assertRedirect('/inventory');
        
        // Verificar que el stock disminuyó
        $item->refresh();
        $this->assertEquals(90, $item->cantidad_stock);
    }
}
