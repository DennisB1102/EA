<?php

namespace App\Http\Controllers;

use App\Models\EquipmentInventory;
use App\Models\Material;
use App\Models\Vehicle;
use App\Models\VehicleEquipment;
use Illuminate\Http\Request;

class EquipmentInventoryController extends Controller
{
    public function create(Vehicle $vehicle, VehicleEquipment $equipment)
    {
        $materials = Material::where('is_active', true)->orderBy('name')->get();
        return view('vehicles.equipment.inventory.create', compact('vehicle', 'equipment', 'materials'));
    }

    public function store(Request $request, Vehicle $vehicle, VehicleEquipment $equipment)
    {
        $data = $request->validate([
            'material_id'      => 'required|exists:materials,id',
            'target_quantity'  => 'required|integer|min:1',
            'current_quantity' => 'required|integer|min:0',
            'expiry_date'      => 'nullable|date',
            'notes'            => 'nullable|string',
        ]);

        $equipment->inventory()->create($data);

        return redirect()->route('vehicles.equipment.show', [$vehicle, $equipment])->with('success', 'Material wurde verlastet.');
    }

    public function edit(Vehicle $vehicle, VehicleEquipment $equipment, EquipmentInventory $inventory)
    {
        $materials = Material::where('is_active', true)->orderBy('name')->get();
        return view('vehicles.equipment.inventory.edit', compact('vehicle', 'equipment', 'inventory', 'materials'));
    }

    public function update(Request $request, Vehicle $vehicle, VehicleEquipment $equipment, EquipmentInventory $inventory)
    {
        $data = $request->validate([
            'material_id'      => 'required|exists:materials,id',
            'target_quantity'  => 'required|integer|min:1',
            'current_quantity' => 'required|integer|min:0',
            'expiry_date'      => 'nullable|date',
            'notes'            => 'nullable|string',
        ]);

        $inventory->update($data);

        return redirect()->route('vehicles.equipment.show', [$vehicle, $equipment])->with('success', 'Eintrag wurde aktualisiert.');
    }

    public function destroy(Vehicle $vehicle, VehicleEquipment $equipment, EquipmentInventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('vehicles.equipment.show', [$vehicle, $equipment])->with('success', 'Material wurde entfernt.');
    }
}
