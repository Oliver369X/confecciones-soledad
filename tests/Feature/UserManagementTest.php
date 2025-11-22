<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users_list()
    {
        $admin = User::factory()->propietario()->create();

        $response = $this->actingAs($admin)->get('/users');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_user()
    {
        $admin = User::factory()->propietario()->create();

        $response = $this->actingAs($admin)->post('/users', [
            'nombre_completo' => 'Nuevo Ayudante',
            'email' => 'ayudante@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'rol' => 'AYUDANTE',
            'telefono' => '75123456',
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', ['email' => 'ayudante@example.com']);
    }

    public function test_admin_can_update_user()
    {
        $admin = User::factory()->propietario()->create();
        $user = User::factory()->cliente()->create();

        $response = $this->actingAs($admin)->put("/users/{$user->usuario_id}", [
            'nombre_completo' => 'Cliente Actualizado',
            'email' => $user->email,
            'rol' => 'CLIENTE',
            'telefono' => '75987654',
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', ['nombre_completo' => 'Cliente Actualizado']);
    }

    public function test_admin_can_delete_user()
    {
        $admin = User::factory()->propietario()->create();
        $user = User::factory()->cliente()->create();

        $response = $this->actingAs($admin)->delete("/users/{$user->usuario_id}");

        $response->assertRedirect('/users');
        $this->assertDatabaseMissing('users', ['usuario_id' => $user->usuario_id]);
    }
}
