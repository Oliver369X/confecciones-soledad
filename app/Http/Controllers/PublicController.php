<?php

namespace App\Http\Controllers;

use App\Models\PortfolioItem;
use App\Models\Order;
use Inertia\Inertia;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        // Página de inicio con trabajos destacados
        $portfolioDestacado = PortfolioItem::where('publicado', true)
            ->latest()
            ->take(6)
            ->get();

        return Inertia::render('Public/Home', [
            'portfolio' => $portfolioDestacado,
        ]);
    }

    public function catalogo()
    {
        // Catálogo completo de trabajos
        $portfolio = PortfolioItem::where('publicado', true)
            ->latest()
            ->paginate(12);

        return Inertia::render('Public/Catalogo', [
            'portfolio' => $portfolio,
        ]);
    }

    public function hacerPedido()
    {
        // Formulario para que el cliente haga un pedido
        return Inertia::render('Public/HacerPedido');
    }

    public function guardarPedido(Request $request)
    {
        $validated = $request->validate([
            'tipo_servicio' => 'required|in:ARREGLO,CONFECCION',
            'descripcion_prenda' => 'required|string|max:500',
            'telefono_contacto' => 'required|string|max:20',
        ]);

        // Si el usuario está autenticado, usa su ID, sino null
        $clienteId = auth()->check() ? auth()->id() : null;

        $pedido = Order::create([
            'cliente_id' => $clienteId,
            'tipo_servicio' => $validated['tipo_servicio'],
            'descripcion_prenda' => $validated['descripcion_prenda'],
            'estado' => 'PENDIENTE_PRESUPUESTO',
            'fecha_solicitud' => now(),
            'presupuesto_total' => 0, // El propietario lo asignará después
        ]);

        return redirect()->route('public.gracias')->with('success', '¡Pedido enviado! Te contactaremos pronto.');
    }

    public function nosotros()
    {
        return Inertia::render('Public/Nosotros');
    }

    public function gracias()
    {
        return Inertia::render('Public/Gracias');
    }
}
