<?php

namespace App\Http\Controllers;

use App\Models\NfrProtocol;
use App\Models\NfrProtocolItem;
use App\Models\Setting;
use App\Models\VehicleEquipment;
use Illuminate\Http\Request;

class QrProtocolController extends Controller
{
    private function sessionKey(int $id): string
    {
        return 'qr_auth_' . $id;
    }

    public function showPassword(VehicleEquipment $equipment)
    {
        if (session($this->sessionKey($equipment->id))) {
            return redirect()->route('qr.create', $equipment);
        }
        return view('qr.password', compact('equipment'));
    }

    public function authenticate(Request $request, VehicleEquipment $equipment)
    {
        $request->validate(['password' => 'required|string']);

        if ($request->input('password') !== Setting::get('nfr_qr_password', config('app.nfr_qr_password'))) {
            return back()->withErrors(['password' => 'Falsches Passwort.']);
        }

        session([$this->sessionKey($equipment->id) => true]);

        return redirect()->route('qr.create', $equipment);
    }

    public function create(VehicleEquipment $equipment)
    {
        if (!session($this->sessionKey($equipment->id))) {
            return redirect()->route('qr.password', $equipment);
        }

        $inventory = $equipment->inventory()
            ->with('material.category')
            ->get()
            ->groupBy(fn ($inv) => $inv->material->category->name);

        $assignedDevices = $equipment->assignedDevices()->with('material')->get();

        return view('qr.create', compact('equipment', 'inventory', 'assignedDevices'));
    }

    public function store(Request $request, VehicleEquipment $equipment)
    {
        if (!session($this->sessionKey($equipment->id))) {
            return redirect()->route('qr.password', $equipment);
        }

        $data = $request->validate([
            'checked_at'         => 'required|date',
            'checked_by'         => 'required|string|max:255',
            'notes'              => 'nullable|string',
            'signature_data'     => 'nullable|string',
            'items'              => 'nullable|array',
            'items.*.is_ok'      => 'nullable',
            'items.*.current_quantity' => 'nullable|integer|min:0',
            'items.*.notes'      => 'nullable|string|max:500',
            'devices'            => 'nullable|array',
            'devices.*.is_ok'    => 'nullable',
            'devices.*.notes'    => 'nullable|string|max:500',
        ]);

        $protocol = NfrProtocol::create([
            'equipment_id'   => $equipment->id,
            'checked_at'     => $data['checked_at'],
            'checked_by'     => $data['checked_by'],
            'notes'          => $data['notes'] ?? null,
            'signature_data' => $data['signature_data'] ?? null,
        ]);

        foreach ($data['items'] ?? [] as $materialId => $item) {
            NfrProtocolItem::create([
                'protocol_id'      => $protocol->id,
                'material_id'      => $materialId,
                'is_ok'            => isset($item['is_ok']),
                'current_quantity' => $item['current_quantity'] ?? null,
                'notes'            => $item['notes'] ?? null,
            ]);
        }

        foreach ($data['devices'] ?? [] as $instanceId => $item) {
            NfrProtocolItem::create([
                'protocol_id'        => $protocol->id,
                'device_instance_id' => $instanceId,
                'is_ok'              => isset($item['is_ok']),
                'notes'              => $item['notes'] ?? null,
            ]);
        }

        return redirect()->route('qr.success', $equipment)
            ->with('qr_done', $protocol->checked_at->format('d.m.Y'));
    }

    public function success(VehicleEquipment $equipment)
    {
        if (!session($this->sessionKey($equipment->id))) {
            return redirect()->route('qr.password', $equipment);
        }

        $date = session('qr_done');
        session()->forget($this->sessionKey($equipment->id));

        return view('qr.success', compact('equipment', 'date'));
    }
}
