<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\MedicationCheckout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicationController extends Controller
{
    private function medicationQuery()
    {
        return Material::with('category')->where('category_id', 5)->where('is_active', true);
    }

    public function index(Request $request)
    {
        $query = $this->medicationQuery();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('article_number', 'like', "%{$request->search}%");
            });
        }

        if ($request->filter === 'low_stock') {
            $query->where('min_stock', '>', 0)
                  ->whereColumn('min_stock', '>',
                      DB::raw('(SELECT COALESCE(SUM(quantity),0) FROM inventory WHERE inventory.material_id = materials.id)'));
        }

        if ($request->filter === 'expiring') {
            $query->whereHas('inventory', function ($q) {
                $q->whereNotNull('expiry_date')
                  ->where('expiry_date', '<=', now()->addDays(30))
                  ->where('quantity', '>', 0);
            });
        }

        $medications = $query->orderBy('name')->paginate(20)->withQueryString();

        // Lade Bestand + Verfall für jede Medikament
        $medications->each(function ($med) {
            $med->load('inventory');
        });

        return view('medications.index', compact('medications'));
    }

    public function show(Material $medication)
    {
        abort_unless($medication->category_id === 5, 404);
        $medication->load('inventory');
        $checkouts = MedicationCheckout::where('material_id', $medication->id)
            ->with('inventory', 'user')
            ->latest()
            ->paginate(20);
        return view('medications.show', compact('medication', 'checkouts'));
    }

    public function create()
    {
        return view('medications.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'article_number' => 'nullable|string|max:100|unique:materials',
            'unit'        => 'required|string|max:50',
            'min_stock'   => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $data['category_id'] = 5;
        $data['has_expiry']  = true;
        $data['min_stock']   = $data['min_stock'] ?? 0;

        $medication = Material::create($data);

        return redirect()->route('medications.show', $medication)->with('success', 'Medikament wurde angelegt.');
    }

    public function edit(Material $medication)
    {
        abort_unless($medication->category_id === 5, 404);
        return view('medications.edit', compact('medication'));
    }

    public function update(Request $request, Material $medication)
    {
        abort_unless($medication->category_id === 5, 404);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'article_number' => 'nullable|string|max:100|unique:materials,article_number,' . $medication->id,
            'unit'        => 'required|string|max:50',
            'min_stock'   => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $medication->update($data);

        return redirect()->route('medications.show', $medication)->with('success', 'Medikament wurde aktualisiert.');
    }

    public function destroy(Material $medication)
    {
        abort_unless($medication->category_id === 5, 404);
        $medication->update(['is_active' => false]);
        return redirect()->route('medications.index')->with('success', 'Medikament wurde deaktiviert.');
    }

    // Bestand (Charge) anlegen
    public function storeBatch(Request $request, Material $medication)
    {
        abort_unless($medication->category_id === 5, 404);

        $data = $request->validate([
            'batch_number' => 'nullable|string|max:100',
            'expiry_date'  => 'required|date|after:today',
            'quantity'     => 'required|integer|min:1',
            'notes'        => 'nullable|string|max:255',
        ]);

        Inventory::create([
            'material_id'  => $medication->id,
            'location_id'  => null,
            'quantity'     => $data['quantity'],
            'expiry_date'  => $data['expiry_date'],
            'batch_number' => $data['batch_number'] ?? null,
            'notes'        => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Charge wurde hinzugefügt.');
    }

    // Charge bearbeiten
    public function updateBatch(Request $request, Material $medication, Inventory $batch)
    {
        abort_unless($medication->category_id === 5 && $batch->material_id === $medication->id, 404);

        $data = $request->validate([
            'batch_number' => 'nullable|string|max:100',
            'expiry_date'  => 'required|date',
            'quantity'     => 'required|integer|min:0',
            'notes'        => 'nullable|string|max:255',
        ]);

        $batch->update($data);

        return back()->with('success', 'Charge wurde aktualisiert.');
    }

    // Charge löschen
    public function destroyBatch(Material $medication, Inventory $batch)
    {
        abort_unless($medication->category_id === 5 && $batch->material_id === $medication->id, 404);
        $batch->delete();
        return back()->with('success', 'Charge wurde gelöscht.');
    }

    // Interne Entnahme
    public function storeCheckout(Request $request, Material $medication)
    {
        abort_unless($medication->category_id === 5, 404);

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
                    'via_qr'       => false,
                    'user_id'      => auth()->id(),
                ]);
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Entnahme wurde erfasst.');
    }
}
