<?php

namespace App\Http\Controllers;

use App\Models\MaterialCategory;
use Illuminate\Http\Request;

class MaterialCategoryController extends Controller
{
    public function index()
    {
        $categories = MaterialCategory::withCount('materials')->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:material_categories',
            'color' => 'required|string|size:7',
            'description' => 'nullable|string',
        ]);

        MaterialCategory::create($data);

        return redirect()->route('categories.index')->with('success', 'Kategorie wurde angelegt.');
    }

    public function show(MaterialCategory $category)
    {
        $category->load('materials');
        return view('categories.show', compact('category'));
    }

    public function edit(MaterialCategory $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, MaterialCategory $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:material_categories,name,' . $category->id,
            'color' => 'required|string|size:7',
            'description' => 'nullable|string',
        ]);

        $category->update($data);

        return redirect()->route('categories.index')->with('success', 'Kategorie wurde aktualisiert.');
    }

    public function destroy(MaterialCategory $category)
    {
        if ($category->materials()->exists()) {
            return back()->with('error', 'Kategorie enthält noch Materialien und kann nicht gelöscht werden.');
        }

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Kategorie wurde gelöscht.');
    }
}
