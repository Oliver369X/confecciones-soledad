<?php

namespace Tests\Feature;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromotionTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_promotions()
    {
        $propietario = User::factory()->propietario()->create();
        Promotion::factory()->count(3)->create();

        $response = $this->actingAs($propietario)->get('/promotions');

        $response->assertStatus(200);
    }

    public function test_owner_can_create_promotion()
    {
        $propietario = User::factory()->propietario()->create();

        $response = $this->actingAs($propietario)->post('/promotions', [
            'codigo' => 'VERANO2024',
            'descripcion' => 'Descuento de verano',
            'tipo_descuento' => 'PORCENTAJE',
            'valor_descuento' => 15,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonth()->format('Y-m-d'),
            'activa' => true,
        ]);

        $response->assertRedirect('/promotions');
        $this->assertDatabaseHas('promociones', [
            'codigo' => 'VERANO2024',
        ]);
    }

    public function test_owner_can_update_promotion()
    {
        $propietario = User::factory()->propietario()->create();
        $promotion = Promotion::factory()->create();

        $response = $this->actingAs($propietario)->put("/promotions/{$promotion->promocion_id}", [
            'codigo' => $promotion->codigo,
            'descripcion' => 'Descripción actualizada',
            'tipo_descuento' => $promotion->tipo_descuento,
            'valor_descuento' => 20,
            'fecha_inicio' => $promotion->fecha_inicio->format('Y-m-d'),
            'fecha_fin' => $promotion->fecha_fin->format('Y-m-d'),
            'activa' => false,
        ]);

        $response->assertRedirect('/promotions');
        $this->assertDatabaseHas('promociones', [
            'promocion_id' => $promotion->promocion_id,
            'activa' => false,
        ]);
    }
}
