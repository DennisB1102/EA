<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use App\Models\Inventory;
use App\Models\Location;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $query = Checkout::with('material', 'location', 'user')->latest();

        if ($request->filled('open')) {
            $query->where('type', 'checkout')->whereNull('returned_at');
        }

        $checkouts = $query->paginate(20)->withQueryString();
        return view('checkouts.index', compact('checkouts'));
    }

    public function create()
    {
        $materials = Material::where('is_active', true)->orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        return view('checkouts.create', compact('materials', 'locations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'location_id' => 'required|exists:locations,id',
            'quantity' => 'required|integer|min:1',
            'purpose' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $inventory = Inventory::where('material_id', $data['material_id'])
                    ->where('location_id', $data['location_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$inventory || $inventory->quantity < $data['quantity']) {
                    throw new \Exception('Nicht genug Bestand vorhanden.');
                }

                $inventory->decrement('quantity', $data['quantity']);

                Checkout::create([
                    ...$data,
                    'user_id' => auth()->id(),
                    'type' => 'checkout',
                ]);
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('checkouts.index')->with('success', 'Entnahme wurde erfasst.');
    }

    public function show(Checkout $checkout)
    {
        $checkout->load('material', 'location', 'user');
        return view('checkouts.show', compact('checkout'));
    }

    public function markReturned(Checkout $checkout)
    {
        if ($checkout->returned_at) {
            return back()->with('error', 'Bereits zurückgegeben.');
        }

        DB::transaction(function () use ($checkout) {
            $inventory = Inventory::firstOrCreate(
                ['material_id' => $checkout->material_id, 'location_id' => $checkout->location_id, 'batch_number' => null],
                ['quantity' => 0]
            );
            $inventory->increment('quantity', $checkout->quantity);
            $checkout->update(['returned_at' => now()]);
        });

        return back()->with('success', 'Material wurde zurückgegeben.');
    }

    public function edit(Checkout $checkout) { abort(404); }
    public function update(Request $request, Checkout $checkout) { abort(404); }
    public function destroy(Checkout $checkout) { abort(404); }
}
