<?php

namespace App\Http\Controllers;

use App\Models\DeviceInstance;
use App\Models\Material;
use App\Models\Vehicle;
use App\Models\VehicleEquipment;
use Illuminate\Http\Request;

class DeviceInstanceController extends Controller
{
    /** Gerät anlegen (aus der Materialdetailseite) */
    public function store(Request $request, Material $material)
    {
        $data = $request->validate([
            'serial_number' => 'nullable|string|max:255',
            'mtk_ablauf'    => 'nullable|date',
            'stk_ablauf'    => 'nullable|date',
            'notes'         => 'nullable|string|max:1000',
        ]);

        $material->deviceInstances()->create($data);

        return back()->with('success', 'Gerät wurde hinzugefügt.');
    }

    /** Gerät bearbeiten (aus der Materialdetailseite) */
    public function update(Request $request, Material $material, DeviceInstance $instance)
    {
        $data = $request->validate([
            'serial_number' => 'nullable|string|max:255',
            'mtk_ablauf'    => 'nullable|date',
            'stk_ablauf'    => 'nullable|date',
            'notes'         => 'nullable|string|max:1000',
        ]);

        $instance->update($data);

        return back()->with('success', 'Gerät wurde aktualisiert.');
    }

    /** Gerät deaktivieren */
    public function destroy(Material $material, DeviceInstance $instance)
    {
        $instance->update(['is_active' => false, 'equipment_id' => null]);
        return back()->with('success', 'Gerät wurde deaktiviert.');
    }

    /** Gerät einem Rucksack zuweisen */
    public function assign(Request $request, Vehicle $vehicle, VehicleEquipment $equipment, DeviceInstance $instance)
    {
        $instance->update(['equipment_id' => $equipment->id]);
        return back()->with('success', 'Gerät wurde zugewiesen.');
    }

    /** Gerät vom Rucksack entfernen */
    public function unassign(Vehicle $vehicle, VehicleEquipment $equipment, DeviceInstance $instance)
    {
        $instance->update(['equipment_id' => null]);
        return back()->with('success', 'Gerät wurde entfernt.');
    }
}
