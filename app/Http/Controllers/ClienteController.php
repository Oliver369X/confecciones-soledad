<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function miCuenta()
    {
        $user = auth()->user();
        
        // Estadísticas del cliente
        $totalPedidos = Order::where('cliente_id', $user->usuario_id)->count();
        $pedidosActivos = Order::where('cliente_id', $user->usuario_id)
            ->whereIn('estado', ['PENDIENTE_PRESUPUESTO', 'CONFIRMADO', 'EN_PROCESO'])
            ->count();
        
        return Inertia::render('Cliente/MiCuenta', [
            'totalPedidos' => $totalPedidos,
            'pedidosActivos' => $pedidosActivos,
        ]);
    }

    public function misPedidos()
    {
        $pedidos = Order::where('cliente_id', auth()->id())
            ->with(['promocion', 'pagos'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Cliente/MisPedidos', [
            'pedidos' => $pedidos,
        ]);
    }

    public function verPedido($id)
    {
        $pedido = Order::where('cliente_id', auth()->id())
            ->where('pedido_id', $id)
            ->with(['promocion', 'pagos'])
            ->firstOrFail();

        return Inertia::render('Cliente/VerPedido', [
            'pedido' => $pedido,
        ]);
    }
    public function catalogo()
    {
        $portfolio = \App\Models\PortfolioItem::where('publicado', true)->latest()->paginate(12);
        return Inertia::render('Cliente/Catalogo', ['portfolio' => $portfolio]);
    }

    public function hacerPedido()
    {
        return Inertia::render('Cliente/HacerPedido');
    }
}
