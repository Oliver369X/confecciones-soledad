<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Order::with(['cliente', 'ayudante']);

        // Filtrar según el rol
        if ($user->rol === 'CLIENTE') {
            $query->where('cliente_id', $user->usuario_id);
        } elseif ($user->rol === 'AYUDANTE') {
            $query->where('ayudante_id', $user->usuario_id);
        }
        // PROPIETARIO ve todo

        $orders = $query->orderBy('fecha_solicitud', 'desc')->get();

        return Inertia::render('Orders/Index', [
            'orders' => $orders
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = User::where('rol', 'CLIENTE')->get();
        $promotions = Promotion::where('activa', true)->get();

        return Inertia::render('Orders/Create', [
            'clients' => $clients,
            'promotions' => $promotions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:users,usuario_id',
            'tipo_servicio' => 'required|in:ARREGLO,CONFECCION',
            'descripcion_prenda' => 'required|string',
            'presupuesto_total' => 'required|numeric|min:0',
            'promocion_id' => 'nullable|exists:promociones,promocion_id',
        ]);

        $monto_descuento = 0;
        if ($request->promocion_id) {
            $promo = Promotion::find($request->promocion_id);
            if ($promo->tipo_descuento === 'PORCENTAJE') {
                $monto_descuento = $request->presupuesto_total * ($promo->valor_descuento / 100);
            } else {
                $monto_descuento = $promo->valor_descuento;
            }
        }

        Order::create([
            'cliente_id' => $request->cliente_id,
            'tipo_servicio' => $request->tipo_servicio,
            'descripcion_prenda' => $request->descripcion_prenda,
            'presupuesto_total' => $request->presupuesto_total,
            'promocion_id' => $request->promocion_id,
            'monto_descuento' => $monto_descuento,
            'estado' => 'PENDIENTE_PRESUPUESTO',
            'fecha_solicitud' => now(),
        ]);

        return redirect()->route('orders.index')->with('success', 'Pedido creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['cliente', 'ayudante', 'promocion', 'pagos', 'resena', 'portafolioItems']);
        return Inertia::render('Orders/Show', [
            'order' => $order
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        // Implementar si es necesario editar detalles básicos
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'estado' => 'required|string',
            'ayudante_id' => 'nullable|exists:users,usuario_id',
            'fecha_entrega_estimada' => 'nullable|date',
        ]);

        $order->update($request->only(['estado', 'ayudante_id', 'fecha_entrega_estimada']));

        if ($request->estado === 'ENTREGADO' && !$order->fecha_entregado) {
            $order->update(['fecha_entregado' => now()]);
        }

        return redirect()->back()->with('success', 'Pedido actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        // Solo propietario puede eliminar
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Pedido eliminado.');
    }
}
