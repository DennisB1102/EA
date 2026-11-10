<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('location')->withCount('equipment')->orderBy('name')->paginate(20);
        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        return view('vehicles.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'location_id'   => 'required|exists:locations,id',
            'name'          => 'required|string|max:255',
            'call_sign'     => 'nullable|string|max:255',
            'license_plate' => 'nullable|string|max:20',
            'tuev_date'     => 'nullable|date',
            'notes'         => 'nullable|string',
            'image'         => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('vehicles', 'public');
        }
        unset($data['image']);

        Vehicle::create($data);

        return redirect()->route('vehicles.index')->with('success', 'Fahrzeug wurde angelegt.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load([
            'location',
            'equipment.inventory.material.category',
            'equipment.protocols.items',
            'equipment.assignedDevices.material',
        ]);
        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        return view('vehicles.edit', compact('vehicle', 'locations'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'location_id'   => 'required|exists:locations,id',
            'name'          => 'required|string|max:255',
            'call_sign'     => 'nullable|string|max:255',
            'license_plate' => 'nullable|string|max:20',
            'tuev_date'     => 'nullable|date',
            'notes'         => 'nullable|string',
            'is_active'     => 'boolean',
            'image'         => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image')) {
            if ($vehicle->image_path) {
                Storage::disk('public')->delete($vehicle->image_path);
            }
            $data['image_path'] = $request->file('image')->store('vehicles', 'public');
        }
        unset($data['image']);

        $data['is_active'] = $request->boolean('is_active');
        $vehicle->update($data);

        return redirect()->route('vehicles.show', $vehicle)->with('success', 'Fahrzeug wurde aktualisiert.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->update(['is_active' => false]);
        return redirect()->route('vehicles.index')->with('success', 'Fahrzeug wurde deaktiviert.');
    }
}
