<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nombre_completo' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'rol' => 'CLIENTE',
            'telefono' => $this->faker->phoneNumber(),
            'sso_provider' => null,
            'sso_id' => null,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function propietario()
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'PROPIETARIO',
        ]);
    }

    public function ayudante()
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'AYUDANTE',
        ]);
    }

    public function cliente()
    {
        return $this->state(fn (array $attributes) => [
            'rol' => 'CLIENTE',
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
