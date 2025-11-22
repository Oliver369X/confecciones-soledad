<?php

namespace Database\Factories;

use App\Models\PortfolioItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class PortfolioItemFactory extends Factory
{
    protected $model = PortfolioItem::class;

    public function definition(): array
    {
        return [
            'pedido_id' => null,
            'titulo' => $this->faker->sentence(3),
            'descripcion' => $this->faker->paragraph(),
            'imagen_url_principal' => $this->faker->imageUrl(640, 480, 'fashion'),
            'imagen_url_antes' => $this->faker->imageUrl(640, 480, 'fashion'),
            'imagen_url_despues' => $this->faker->imageUrl(640, 480, 'fashion'),
            'publicado' => true,
        ];
    }
}
