<?php

namespace App\Http\Controllers;

use App\Models\DeviceInstance;
use App\Models\Material;
use App\Models\MaterialCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::with('category')->where('is_active', true)->where('category_id', '!=', 5);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('article_number', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filter === 'low_stock') {
            $query->where('min_stock', '>', 0)
                  ->whereColumn('min_stock', '>',
                      DB::raw('(SELECT COALESCE(SUM(quantity),0) FROM inventory WHERE inventory.material_id = materials.id)'));
        }
        if ($request->filter === 'devices') {
            $query->where('is_device', true);
        }

        $materials = $query->orderBy('name')->paginate(20)->withQueryString();
        $categories = MaterialCategory::orderBy('name')->get();

        return view('materials.index', compact('materials', 'categories'));
    }

    public function create()
    {
        $categories = MaterialCategory::orderBy('name')->get();
        return view('materials.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $isDevice = $request->boolean('is_device');

        $data = $request->validate([
            'name'                     => 'required|string|max:255',
            'article_number'           => 'nullable|string|max:100|unique:materials',
            'category_id'              => 'required|exists:material_categories,id',
            'description'              => 'nullable|string',
            'unit'                     => $isDevice ? 'nullable|string|max:50' : 'required|string|max:50',
            'min_stock'                => 'nullable|integer|min:0',
            'requires_inspection'      => 'boolean',
            'inspection_interval_days' => 'nullable|integer|min:1',
            'has_expiry'               => 'boolean',
            'device.serial_number'     => 'nullable|string|max:255',
            'device.mtk_ablauf'        => 'nullable|date',
            'device.stk_ablauf'        => 'nullable|date',
        ]);

        $data['requires_inspection'] = $request->boolean('requires_inspection');
        $data['has_expiry']          = $request->boolean('has_expiry');
        $data['is_device']           = $isDevice;
        $data['unit']                = $isDevice ? 'Stück' : ($data['unit'] ?? 'Stück');
        $data['min_stock']           = $data['min_stock'] ?? 0;

        $material = Material::create($data);

        if ($isDevice && $request->filled('device')) {
            $dev = $request->input('device');
            if (!empty($dev['serial_number']) || !empty($dev['mtk_ablauf']) || !empty($dev['stk_ablauf'])) {
                DeviceInstance::create([
                    'material_id'   => $material->id,
                    'serial_number' => $dev['serial_number'] ?? null,
                    'mtk_ablauf'    => $dev['mtk_ablauf'] ?? null,
                    'stk_ablauf'    => $dev['stk_ablauf'] ?? null,
                ]);
            }
        }

        return redirect()->route('materials.show', $material)->with('success', 'Material wurde angelegt.');
    }

    public function show(Material $material)
    {
        $material->load('category', 'inventory.location', 'checkouts.user', 'inspectionLogs.user', 'deviceInstances');
        return view('materials.show', compact('material'));
    }

    public function edit(Material $material)
    {
        $categories = MaterialCategory::orderBy('name')->get();
        return view('materials.edit', compact('material', 'categories'));
    }

    public function update(Request $request, Material $material)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'article_number' => 'nullable|string|max:100|unique:materials,article_number,' . $material->id,
            'category_id' => 'required|exists:material_categories,id',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'min_stock' => 'required|integer|min:0',
            'requires_inspection' => 'boolean',
            'inspection_interval_days' => 'nullable|integer|min:1',
            'has_expiry' => 'boolean',
        ]);

        $data['requires_inspection'] = $request->boolean('requires_inspection');
        $data['has_expiry']          = $request->boolean('has_expiry');
        $data['is_device']           = $request->boolean('is_device');

        $material->update($data);

        return redirect()->route('materials.show', $material)->with('success', 'Material wurde aktualisiert.');
    }

    public function destroy(Material $material)
    {
        $material->update(['is_active' => false]);
        return redirect()->route('materials.index')->with('success', 'Material wurde deaktiviert.');
    }
}
