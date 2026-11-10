<?php

namespace App\Http\Controllers;

use App\Models\NfrProtocol;
use App\Models\NfrProtocolItem;
use App\Models\Vehicle;
use App\Models\VehicleEquipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NfrProtocolController extends Controller
{
    public function index(Vehicle $vehicle, VehicleEquipment $equipment)
    {
        $protocols = $equipment->protocols()->latest('checked_at')->get();
        return view('vehicles.equipment.protocols.index', compact('vehicle', 'equipment', 'protocols'));
    }

    public function create(Vehicle $vehicle, VehicleEquipment $equipment)
    {
        $inventory = $equipment->inventory()
            ->with('material.category')
            ->get()
            ->groupBy(fn ($inv) => $inv->material->category->name);

        $assignedDevices = $equipment->assignedDevices()->with('material')->get();

        return view('vehicles.equipment.protocols.create', compact('vehicle', 'equipment', 'inventory', 'assignedDevices'));
    }

    public function store(Request $request, Vehicle $vehicle, VehicleEquipment $equipment)
    {
        $data = $request->validate([
            'checked_at'               => 'required|date',
            'checked_by'               => 'required|string|max:255',
            'notes'                    => 'nullable|string',
            'signature_data'           => 'nullable|string',
            'items'                    => 'nullable|array',
            'items.*.is_ok'            => 'nullable',
            'items.*.current_quantity' => 'nullable|integer|min:0',
            'items.*.notes'            => 'nullable|string|max:500',
            'items.*.photo'            => 'nullable|image|max:8192',
            'devices'                  => 'nullable|array',
            'devices.*.is_ok'          => 'nullable',
            'devices.*.notes'          => 'nullable|string|max:500',
            'devices.*.photo'          => 'nullable|image|max:8192',
        ]);

        $protocol = NfrProtocol::create([
            'equipment_id'   => $equipment->id,
            'checked_at'     => $data['checked_at'],
            'checked_by'     => $data['checked_by'],
            'notes'          => $data['notes'] ?? null,
            'signature_data' => $data['signature_data'] ?? null,
        ]);

        foreach ($data['items'] ?? [] as $materialId => $item) {
            $photo = $request->file("items.{$materialId}.photo");
            NfrProtocolItem::create([
                'protocol_id'      => $protocol->id,
                'material_id'      => $materialId,
                'is_ok'            => isset($item['is_ok']),
                'current_quantity' => $item['current_quantity'] ?? null,
                'notes'            => $item['notes'] ?? null,
                'photo_path'       => $photo ? $photo->store('nfr_photos', 'public') : null,
            ]);
        }

        foreach ($data['devices'] ?? [] as $instanceId => $item) {
            $photo = $request->file("devices.{$instanceId}.photo");
            NfrProtocolItem::create([
                'protocol_id'        => $protocol->id,
                'device_instance_id' => $instanceId,
                'is_ok'              => isset($item['is_ok']),
                'notes'              => $item['notes'] ?? null,
                'photo_path'         => $photo ? $photo->store('nfr_photos', 'public') : null,
            ]);
        }

        return redirect()
            ->route('vehicles.equipment.protocols.show', [$vehicle, $equipment, $protocol])
            ->with('success', 'Prüfprotokoll gespeichert.');
    }

    public function resolveItem(Request $request, Vehicle $vehicle, VehicleEquipment $equipment, NfrProtocol $protocol, NfrProtocolItem $item)
    {
        $data = $request->validate([
            'resolved_by'      => 'required|string|max:255',
            'resolution_notes' => 'nullable|string|max:1000',
        ]);

        $item->update([
            'resolved_at'      => now(),
            'resolved_by'      => $data['resolved_by'],
            'resolution_notes' => $data['resolution_notes'] ?? null,
        ]);

        return back()->with('success', 'Mangel als behoben markiert.');
    }

    public function allIndex()
    {
        $protocols = NfrProtocol::with(['equipment.vehicle'])
            ->latest('checked_at')
            ->get()
            ->groupBy(fn ($p) => $p->equipment->name);

        $nfrs = VehicleEquipment::where('type', 'notfallrucksack')
            ->with('vehicle')
            ->orderBy('name')
            ->get();

        return view('vehicles.equipment.protocols.all', compact('protocols', 'nfrs'));
    }

    public function printView(Vehicle $vehicle, VehicleEquipment $equipment, NfrProtocol $protocol)
    {
        $protocol->load(['items.material.category', 'items.deviceInstance.material']);
        $materialItems = $protocol->items->whereNull('device_instance_id')->groupBy(fn ($i) => $i->material->category->name);
        $deviceItems   = $protocol->items->whereNotNull('device_instance_id');
        return view('vehicles.equipment.protocols.print', compact('vehicle', 'equipment', 'protocol', 'materialItems', 'deviceItems'));
    }

    public function show(Vehicle $vehicle, VehicleEquipment $equipment, NfrProtocol $protocol)
    {
        $protocol->load(['items.material.category', 'items.deviceInstance.material']);
        $materialItems = $protocol->items->whereNull('device_instance_id')->groupBy(fn ($i) => $i->material->category->name);
        $deviceItems   = $protocol->items->whereNotNull('device_instance_id');
        return view('vehicles.equipment.protocols.show', compact('vehicle', 'equipment', 'protocol', 'materialItems', 'deviceItems'));
    }
}
