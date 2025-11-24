<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ordenar por fecha de creación (usando columna 'fecha' o 'created_at')
        $reviews = Review::with(['cliente', 'pedido'])->orderBy('fecha', 'desc')->get();
        return Inertia::render('Reviews/Index', [
            'reviews' => $reviews
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,pedido_id',
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string',
        ]);

        // Verificar que el pedido pertenece al usuario y está entregado
        $pedido = Order::findOrFail($request->pedido_id);
        
        if ($pedido->cliente_id !== Auth::id()) {
            abort(403, 'No tienes permiso para reseñar este pedido.');
        }

        if ($pedido->estado !== 'ENTREGADO') {
            return redirect()->back()->withErrors(['pedido_id' => 'Solo se pueden reseñar pedidos entregados.']);
        }

        if (Review::where('pedido_id', $pedido->pedido_id)->exists()) {
            return redirect()->back()->withErrors(['pedido_id' => 'Ya existe una reseña para este pedido.']);
        }

        Review::create([
            'pedido_id' => $request->pedido_id,
            'cliente_id' => Auth::id(),
            'calificacion' => $request->calificacion,
            'comentario' => $request->comentario,
        ]);

        return redirect()->back()->with('success', 'Reseña enviada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        // Solo propietario o el autor pueden eliminar (opcional)
        if (Auth::user()->rol !== 'PROPIETARIO' && Auth::id() !== $review->cliente_id) {
            abort(403);
        }

        $review->delete();
        return redirect()->back()->with('success', 'Reseña eliminada.');
    }
}
