<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Material;
use App\Models\MedicationCheckout;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicationQrController extends Controller
{
    private function sessionKey(int $id): string
    {
        return "med_qr_auth_{$id}";
    }

    public function showPassword(Material $medication)
    {
        abort_unless($medication->category_id === 5 && $medication->is_active, 404);
        return view('med-qr.password', compact('medication'));
    }

    public function authenticate(Request $request, Material $medication)
    {
        abort_unless($medication->category_id === 5 && $medication->is_active, 404);

        $password = Setting::get('nfr_qr_password', '');
        if ($password && $request->input('password') !== $password) {
            return back()->withErrors(['password' => 'Passwort falsch.']);
        }

        session([$this->sessionKey($medication->id) => true]);
        return redirect()->route('med-qr.create', $medication);
    }

    public function create(Request $request, Material $medication)
    {
        abort_unless($medication->category_id === 5 && $medication->is_active, 404);
        abort_unless(session($this->sessionKey($medication->id)), 403);

        $batches = Inventory::where('material_id', $medication->id)
            ->where('quantity', '>', 0)
            ->orderBy('expiry_date')
            ->get();

        return view('med-qr.create', compact('medication', 'batches'));
    }

    public function store(Request $request, Material $medication)
    {
        abort_unless($medication->category_id === 5 && $medication->is_active, 404);
        abort_unless(session($this->sessionKey($medication->id)), 403);

        $data = $request->validate([
            'inventory_id' => 'required|exists:inventory,id',
            'quantity'     => 'required|integer|min:1',
            'taken_by'     => 'required|string|max:255',
            'reason'       => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($data, $medication) {
                $inv = Inventory::where('id', $data['inventory_id'])
                    ->where('material_id', $medication->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($inv->quantity < $data['quantity']) {
                    throw new \Exception('Nicht genug Bestand in dieser Charge.');
                }

                $inv->decrement('quantity', $data['quantity']);

                MedicationCheckout::create([
                    'material_id'  => $medication->id,
                    'inventory_id' => $inv->id,
                    'quantity'     => $data['quantity'],
                    'taken_by'     => $data['taken_by'],
                    'reason'       => $data['reason'] ?? null,
                    'via_qr'       => true,
                    'user_id'      => null,
                ]);
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        session()->forget($this->sessionKey($medication->id));
        return redirect()->route('med-qr.success', $medication);
    }

    public function success(Material $medication)
    {
        abort_unless($medication->category_id === 5, 404);
        return view('med-qr.success', compact('medication'));
    }
}
