<?php

namespace App\Http\Controllers;

use App\Models\PortfolioItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = PortfolioItem::where('publicado', true)->get();
        return Inertia::render('Portfolio/Index', [
            'items' => $items
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Portfolio/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen_url_principal' => 'required|string', // In a real app this would be an image upload
            'imagen_url_antes' => 'nullable|string',
            'imagen_url_despues' => 'nullable|string',
        ]);

        PortfolioItem::create($request->all());

        return redirect()->route('portfolio.index')->with('success', 'Item de portafolio creado.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PortfolioItem $portfolio)
    {
        return Inertia::render('Portfolio/Edit', [
            'item' => $portfolio
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PortfolioItem $portfolio)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen_url_principal' => 'required|string',
            'imagen_url_antes' => 'nullable|string',
            'imagen_url_despues' => 'nullable|string',
            'publicado' => 'boolean',
        ]);

        $portfolio->update($request->all());

        return redirect()->route('portfolio.index')->with('success', 'Item de portafolio actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PortfolioItem $portfolio)
    {
        $portfolio->delete();
        return redirect()->route('portfolio.index')->with('success', 'Item de portafolio eliminado.');
    }
}
