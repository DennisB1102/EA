<?php

namespace App\Http\Controllers;

use App\Models\InspectionLog;
use App\Models\Location;
use App\Models\Material;
use Illuminate\Http\Request;

class InspectionLogController extends Controller
{
    public function index(Request $request)
    {
        $query = InspectionLog::with('material', 'location', 'user')->latest();

        if ($request->filled('material')) {
            $query->where('material_id', $request->material);
        }

        $inspections = $query->paginate(20)->withQueryString();
        $materials = Material::where('is_active', true)->where('requires_inspection', true)->orderBy('name')->get();

        return view('inspections.index', compact('inspections', 'materials'));
    }

    public function create()
    {
        $materials = Material::where('is_active', true)->where('requires_inspection', true)->orderBy('name')->get();
        $locations = Location::where('is_active', true)->orderBy('name')->get();
        return view('inspections.create', compact('materials', 'locations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'location_id' => 'required|exists:locations,id',
            'result' => 'required|in:ok,defect,replaced',
            'notes' => 'nullable|string',
            'next_inspection_due' => 'nullable|date|after:today',
        ]);

        $data['user_id'] = auth()->id();

        InspectionLog::create($data);

        return redirect()->route('inspections.index')->with('success', 'Prüfprotokoll wurde erstellt.');
    }

    public function show(InspectionLog $inspection)
    {
        $inspection->load('material', 'location', 'user');
        return view('inspections.show', compact('inspection'));
    }

    public function edit(InspectionLog $inspection) { abort(404); }
    public function update(Request $request, InspectionLog $inspection) { abort(404); }
    public function destroy(InspectionLog $inspection) { abort(404); }
}
