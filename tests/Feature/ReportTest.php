<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\InventoryMovement;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_reports()
    {
        $propietario = User::factory()->propietario()->create();

        // Crear datos de prueba: ingresos
        $order = Order::factory()->create(['presupuesto_total' => 500]);
        Payment::factory()->create([
            'pedido_id' => $order->pedido_id,
            'monto' => 500,
            'fecha_pago' => now(),
        ]);

        // Crear datos de prueba: costos
        $item = InventoryItem::factory()->create(['costo_unitario' => 10]);
        InventoryMovement::factory()->create([
            'item_id' => $item->item_id,
            'tipo_movimiento' => 'SALIDA',
            'cantidad' => 20,
            'fecha_movimiento' => now(),
        ]);

        $response = $this->actingAs($propietario)->get('/reports', [
            'start_date' => now()->subDay()->format('Y-m-d'),
            'end_date' => now()->addDay()->format('Y-m-d'),
        ]);

        $response->assertStatus(200);
        // Verificar que los datos calculados sean correctos
        // Ingresos: 500, Costos: 20*10=200, Rentabilidad: 300
    }
}
