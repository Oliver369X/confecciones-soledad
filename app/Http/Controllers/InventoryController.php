<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = InventoryItem::all();
        return Inertia::render('Inventory/Index', [
            'items' => $items
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_material' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'cantidad_stock' => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:50',
            'costo_unitario' => 'required|numeric|min:0',
        ]);

        $item = InventoryItem::create($request->all());

        // Registrar movimiento inicial si hay stock
        if ($request->cantidad_stock > 0) {
            InventoryMovement::create([
                'item_id' => $item->item_id,
                'usuario_id' => Auth::id(),
                'tipo_movimiento' => 'ENTRADA',
                'cantidad' => $request->cantidad_stock,
                'fecha_movimiento' => now(),
                'motivo' => 'Inventario Inicial',
            ]);
        }

        return redirect()->route('inventory.index')->with('success', 'Material registrado exitosamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InventoryItem $inventory)
    {
        $request->validate([
            'nombre_material' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string|max:50',
            'costo_unitario' => 'required|numeric|min:0',
        ]);

        $inventory->update($request->all());

        return redirect()->route('inventory.index')->with('success', 'Material actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryItem $inventory)
    {
        $inventory->delete();
        return redirect()->route('inventory.index')->with('success', 'Material eliminado.');
    }

    /**
     * Registrar un movimiento de inventario (Entrada/Salida)
     */
    public function storeMovement(Request $request, InventoryItem $inventory)
    {
        $request->validate([
            'tipo_movimiento' => 'required|in:ENTRADA,SALIDA',
            'cantidad' => 'required|numeric|min:0.01',
            'motivo' => 'nullable|string',
            'pedido_id' => 'nullable|exists:pedidos,pedido_id',
        ]);

        if ($request->tipo_movimiento === 'SALIDA' && $inventory->cantidad_stock < $request->cantidad) {
            return redirect()->back()->withErrors(['cantidad' => 'Stock insuficiente.']);
        }

        // Crear movimiento
        InventoryMovement::create([
            'item_id' => $inventory->item_id,
            'usuario_id' => Auth::id(),
            'pedido_id' => $request->pedido_id,
            'tipo_movimiento' => $request->tipo_movimiento,
            'cantidad' => $request->cantidad,
            'fecha_movimiento' => now(),
            'motivo' => $request->motivo,
        ]);

        // Actualizar stock
        if ($request->tipo_movimiento === 'ENTRADA') {
            $inventory->increment('cantidad_stock', $request->cantidad);
        } else {
            $inventory->decrement('cantidad_stock', $request->cantidad);
        }

        return redirect()->route('inventory.index')->with('success', 'Movimiento registrado.');
    }
}
