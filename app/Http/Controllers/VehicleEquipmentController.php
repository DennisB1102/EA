<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleEquipment;
use Illuminate\Http\Request;

class VehicleEquipmentController extends Controller
{
    public function create(Vehicle $vehicle)
    {
        $types = VehicleEquipment::TYPES;
        return view('vehicles.equipment.create', compact('vehicle', 'types'));
    }

    public function store(Request $request, Vehicle $vehicle)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|string|in:' . implode(',', array_keys(VehicleEquipment::TYPES)),
            'description' => 'nullable|string',
        ]);

        $vehicle->equipment()->create($data);

        return redirect()->route('vehicles.show', $vehicle)->with('success', 'Ausstattung wurde hinzugefügt.');
    }

    public function edit(Vehicle $vehicle, VehicleEquipment $equipment)
    {
        $types = VehicleEquipment::TYPES;
        return view('vehicles.equipment.edit', compact('vehicle', 'equipment', 'types'));
    }

    public function update(Request $request, Vehicle $vehicle, VehicleEquipment $equipment)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|string|in:' . implode(',', array_keys(VehicleEquipment::TYPES)),
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $equipment->update($data);

        return redirect()->route('vehicles.equipment.show', [$vehicle, $equipment])->with('success', 'Ausstattung wurde aktualisiert.');
    }

    public function show(Vehicle $vehicle, VehicleEquipment $equipment)
    {
        $equipment->load('inventory.material.category', 'assignedDevices.material');

        $availableDevices = \App\Models\DeviceInstance::with('material')
            ->where('is_active', true)
            ->whereNull('equipment_id')
            ->get()
            ->groupBy(fn ($d) => $d->material->name);

        return view('vehicles.equipment.show', compact('vehicle', 'equipment', 'availableDevices'));
    }

    public function destroy(Vehicle $vehicle, VehicleEquipment $equipment)
    {
        $equipment->delete();
        return redirect()->route('vehicles.show', $vehicle)->with('success', 'Ausstattung wurde gelöscht.');
    }
}
