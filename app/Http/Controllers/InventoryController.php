<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Location;
use App\Models\Material;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::with('material.category', 'location');

        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }
        if ($request->filled('expiring')) {
            $query->whereNotNull('expiry_date')->where('expiry_date', '<=', now()->addDays(30));
        }

        $inventory = $query->orderBy('updated_at', 'desc')->paginate(20)->withQueryString();
        $locations = Location::where('is_active', true)->orderBy('name')->get();

        return view('inventory.index', compact('inventory', 'locations'));
    }

    public function create()
    {
        $materials = Material::where('is_active', true)->orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        return view('inventory.create', compact('materials', 'locations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'location_id' => 'required|exists:locations,id',
            'quantity' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $inventory = Inventory::where('material_id', $data['material_id'])
            ->where('location_id', $data['location_id'])
            ->where('batch_number', $data['batch_number'] ?? null)
            ->first();

        if ($inventory) {
            $inventory->increment('quantity', $data['quantity']);
        } else {
            Inventory::create($data);
        }

        return redirect()->route('inventory.index')->with('success', 'Bestand wurde aktualisiert.');
    }

    public function show(Inventory $inventory)
    {
        $inventory->load('material.category', 'location');
        return view('inventory.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        $materials = Material::where('is_active', true)->orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        return view('inventory.edit', compact('inventory', 'materials', 'locations'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
            'batch_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $inventory->update($data);

        return redirect()->route('inventory.index')->with('success', 'Bestand wurde aktualisiert.');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('inventory.index')->with('success', 'Bestandseintrag wurde gelöscht.');
    }
}
