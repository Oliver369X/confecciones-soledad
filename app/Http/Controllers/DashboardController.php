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
        $user = auth()->user();

        // Lógica para CLIENTES
        if ($user->rol === 'CLIENTE') {
            $stats = [
                'pedidos_activos' => Order::where('cliente_id', $user->usuario_id)
                    ->whereIn('estado', ['PENDIENTE_PRESUPUESTO', 'CONFIRMADO', 'EN_PROCESO'])
                    ->count(),
                'total_gastado' => Payment::whereHas('pedido', function($q) use ($user) {
                        $q->where('cliente_id', $user->usuario_id);
                    })->sum('monto'),
                'mensajes_nuevos' => 0, // Placeholder
                'ultimo_pedido' => Order::where('cliente_id', $user->usuario_id)->latest()->first(),
            ];

            return Inertia::render('Dashboard', [
                'stats' => $stats,
                'role' => 'CLIENTE'
            ]);
        }

        // Lógica para ADMIN/AYUDANTE
        // 1. Pedidos de Hoy
        $pedidosHoy = Order::whereDate('created_at', Carbon::today())->count();

        // 2. En Proceso
        $enProceso = Order::whereIn('estado', ['PENDIENTE', 'EN_PROCESO', 'En Proceso'])->count();

        // 3. Ingresos del Mes
        $ingresosMes = Payment::whereMonth('fecha_pago', Carbon::now()->month)
            ->whereYear('fecha_pago', Carbon::now()->year)
            ->sum('monto');

        // 4. Stock Bajo
        $stockBajo = InventoryItem::where('stock_actual', '<=', 5)->count();

        // 5. Datos para Gráfico (Últimos 6 meses)
        $chartData = [];
        $labels = [];
        $data = [];
        
        for ($i = 5; $i >= 0; $i--) {
             $date = Carbon::now()->subMonths($i);
             $monthName = $date->translatedFormat('M'); // Requiere locale configurado, o usar format('M')
             $revenue = Payment::whereMonth('fecha_pago', $date->month)
                ->whereYear('fecha_pago', $date->year)
                ->sum('monto');
             
             $labels[] = $date->format('M');
             $data[] = $revenue;
        }

        return Inertia::render('Dashboard', [
            'stats' => [
                'pedidos_hoy' => $pedidosHoy,
                'en_proceso' => $enProceso,
                'ingresos_mes' => $ingresosMes,
                'stock_bajo' => $stockBajo,
            ],
            'chartData' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Ingresos (Bs)',
                        'backgroundColor' => '#6366F1',
                        'data' => $data
                    ]
                ]
            ],
            'role' => $user->rol
        ]);
    }
}
