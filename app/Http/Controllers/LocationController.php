<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::withCount('inventory')->orderBy('name')->paginate(20);
        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Location::create($data);

        return redirect()->route('locations.index')->with('success', 'Standort wurde angelegt.');
    }

    public function show(Location $location)
    {
        $location->load('inventory.material.category');
        return view('locations.show', compact('location'));
    }

    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $location->update($data);

        return redirect()->route('locations.index')->with('success', 'Standort wurde aktualisiert.');
    }

    public function destroy(Location $location)
    {
        $location->update(['is_active' => false]);
        return redirect()->route('locations.index')->with('success', 'Standort wurde deaktiviert.');
    }
}
