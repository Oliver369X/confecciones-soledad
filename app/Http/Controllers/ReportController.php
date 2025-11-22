<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\InventoryMovement;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // 1. Calcular Ingresos (Pagos confirmados)
        $ingresos = Payment::whereBetween('fecha_pago', [$startDate, $endDate])
            ->sum('monto');

        // 2. Calcular Costos de Insumos (Salidas de inventario * Costo Unitario)
        // Nota: Esto es una aproximación. Idealmente se usaría el costo promedio ponderado o FIFO.
        // Aquí usaremos el costo actual del ítem al momento del reporte.
        $movimientosSalida = InventoryMovement::with('item')
            ->where('tipo_movimiento', 'SALIDA')
            ->whereBetween('fecha_movimiento', [$startDate, $endDate])
            ->get();

        $costos = 0;
        foreach ($movimientosSalida as $movimiento) {
            if ($movimiento->item) {
                $costos += $movimiento->cantidad * $movimiento->item->costo_unitario;
            }
        }

        // 3. Rentabilidad
        $rentabilidad = $ingresos - $costos;

        return Inertia::render('Reports/Index', [
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'stats' => [
                'ingresos' => $ingresos,
                'costos' => $costos,
                'rentabilidad' => $rentabilidad,
            ]
        ]);
    }
}
