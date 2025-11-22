<?php

namespace Database\Seeders;

use App\Models\PortfolioItem;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'titulo' => 'Vestido de Novia Elegante',
                'descripcion' => 'Hermoso vestido de novia confeccionado a medida con encajes delicados y detalles en pedrería.',
                'imagen_url_principal' => 'https://images.unsplash.com/photo-1519741497674-611481863552?w=800',
                'imagen_url_antes' => null,
                'imagen_url_despues' => null,
                'publicado' => true,
            ],
            [
                'titulo' => 'Traje de Gala Masculino',
                'descripcion' => 'Traje de gala oscuro con corte moderno, perfecto para eventos formales.',
                'imagen_url_principal' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=800',
                'imagen_url_antes' => null,
                'imagen_url_despues' => null,
                'publicado' => true,
            ],
            [
                'titulo' => 'Ajuste de Pantalón',
                'descripcion' => 'Servicio de ajuste profesional para pantalones de vestir.',
                'imagen_url_principal' => 'https://images.unsplash.com/photo-1473691955023-da1c49c95c78?w=800',
                'imagen_url_antes' => 'https://images.unsplash.com/photo-1473691955023-da1c49c95c78?w=400&sat=-100',
                'imagen_url_despues' => 'https://images.unsplash.com/photo-1473691955023-da1c49c95c78?w=400',
                'publicado' => true,
            ],
            [
                'titulo' => 'Blusa Casual Moderna',
                'descripcion' => 'Diseño y confección de blusa casual con telas premium.',
                'imagen_url_principal' => 'https://images.unsplash.com/photo-1485968579580-b6d095142e6e?w=800',
                'imagen_url_antes' => null,
                'imagen_url_despues' => null,
                'publicado' => true,
            ],
            [
                'titulo' => 'Vestido de Fiesta',
                'descripcion' => 'Vestido de fiesta en tonos vibrantes, ideal para celebraciones especiales.',
                'imagen_url_principal' => 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=800',
                'imagen_url_antes' => null,
                'imagen_url_despues' => null,
                'publicado' => true,
            ],
            [
                'titulo' => 'Camisa a Medida',
                'descripcion' => 'Camisa de vestir confeccionada a medida con telas de alta calidad.',
                'imagen_url_principal' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=800',
                'imagen_url_antes' => null,
                'imagen_url_despues' => null,
                'publicado' => true,
            ],
            [
                'titulo' => 'Arreglo de Vestido de Fiesta',
                'descripcion' => 'Ajuste y modificación de vestido de fiesta para el ajuste perfecto.',
                'imagen_url_principal' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=800',
                'imagen_url_antes' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400&sat=-100',
                'imagen_url_despues' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400',
                'publicado' => true,
            ],
            [
                'titulo' => 'Conjunto Deportivo Personalizado',
                'descripcion' => 'Ropa deportiva con diseños personalizados y logos bordados.',
                'imagen_url_principal' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=800',
                'imagen_url_antes' => null,
                'imagen_url_despues' => null,
                'publicado' => true,
            ],
            [
                'titulo' => 'Falda Plisada Elegante',
                'descripcion' => 'Falda plisada confeccionada con técnicas tradicionales.',
                'imagen_url_principal' => 'https://images.unsplash.com/photo-1583496661160-fb5886a0aaaa?w=800',
                'imagen_url_antes' => null,
                'imagen_url_despues' => null,
                'publicado' => true,
            ],
            [
                'titulo' => 'Chaqueta de Cuero Ajustada',
                'descripcion' => 'Ajuste profesional de chaqueta de cuero para el fit perfecto.',
                'imagen_url_principal' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800',
                'imagen_url_antes' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400&blur=20',
                'imagen_url_despues' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400',
                'publicado' => true,
            ],
            [
                'titulo' => 'Vestido Infantil',
                'descripcion' => 'Adorable vestido para niñas con diseños coloridos y cómodos.',
                'imagen_url_principal' => 'https://images.unsplash.com/photo-1518831959646-742c3a14ebf7?w=800',
                'imagen_url_antes' => null,
                'imagen_url_despues' => null,
                'publicado' => true,
            ],
            [
                'titulo' => 'Pantalón de Vestir Slim Fit',
                'descripcion' => 'Pantalón de vestir moderno con corte slim fit.',
                'imagen_url_principal' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=800',
                'imagen_url_antes' => null,
                'imagen_url_despues' => null,
                'publicado' => true,
            ],
            [
                'titulo' => 'Bordado Personalizado',
                'descripcion' => 'Servicio de bordado a mano para personalizar tus prendas.',
                'imagen_url_principal' => 'https://images.unsplash.com/photo-1558769132-cb1aea78c3d4?w=800',
                'imagen_url_antes' => null,
                'imagen_url_despues' => null,
                'publicado' => true,
            ],
            [
                'titulo' => 'Uniforme Escolar',
                'descripcion' => 'Confección de uniformes escolares con especificaciones exactas.',
                'imagen_url_principal' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=800',
                'imagen_url_antes' => null,
                'imagen_url_despues' => null,
                'publicado' => true,
            ],
            [
                'titulo' => 'Traje de Baño Deportivo',
                'descripcion' => 'Diseño y confección de trajes de baño deportivos con telas especiales.',
                'imagen_url_principal' => 'https://images.unsplash.com/photo-1582552938357-32b906df40cb?w=800',
                'imagen_url_antes' => null,
                'imagen_url_despues' => null,
                'publicado' => true,
            ],
        ];

        foreach ($items as $item) {
            PortfolioItem::create($item);
        }
    }
}
