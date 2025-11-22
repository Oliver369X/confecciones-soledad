<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promotions = Promotion::orderBy('fecha_inicio', 'desc')->get();
        return Inertia::render('Promotions/Index', [
            'promotions' => $promotions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|unique:promociones,codigo|max:50',
            'descripcion' => 'nullable|string',
            'tipo_descuento' => 'required|in:PORCENTAJE,MONTO_FIJO',
            'valor_descuento' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'activa' => 'boolean',
        ]);

        Promotion::create($request->all());

        return redirect()->route('promotions.index')->with('success', 'Promoción creada exitosamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promotion $promotion)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:promociones,codigo,' . $promotion->promocion_id . ',promocion_id',
            'descripcion' => 'nullable|string',
            'tipo_descuento' => 'required|in:PORCENTAJE,MONTO_FIJO',
            'valor_descuento' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'activa' => 'boolean',
        ]);

        $promotion->update($request->all());

        return redirect()->route('promotions.index')->with('success', 'Promoción actualizada.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        $promotion->delete();
        return redirect()->route('promotions.index')->with('success', 'Promoción eliminada.');
    }
}
