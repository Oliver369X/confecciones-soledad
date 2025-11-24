<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Pedidos de Hoy
        $pedidosHoy = Order::whereDate('created_at', Carbon::today())->count();

        // 2. En Proceso (Pedidos con estado 'PENDIENTE' o 'EN_PROCESO')
        // Ajusta los estados según tu lógica de negocio (ej. 'En Proceso', 'Pendiente')
        $enProceso = Order::whereIn('estado', ['PENDIENTE', 'EN_PROCESO', 'En Proceso'])->count();

        // 3. Ingresos del Mes
        $ingresosMes = Payment::whereMonth('fecha_pago', Carbon::now()->month)
            ->whereYear('fecha_pago', Carbon::now()->year)
            ->sum('monto');

        // 4. Stock Bajo (Items con stock <= 5)
        $stockBajo = InventoryItem::where('stock_actual', '<=', 5)->count();

        return Inertia::render('Dashboard', [
            'stats' => [
                'pedidos_hoy' => $pedidosHoy,
                'en_proceso' => $enProceso,
                'ingresos_mes' => $ingresosMes,
                'stock_bajo' => $stockBajo,
            ]
        ]);
    }
}
