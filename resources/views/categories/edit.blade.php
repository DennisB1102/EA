<x-app-layout>
    @section('title', 'Kategorie bearbeiten')
    <div class="max-w-md">
        <div class="mb-6">
            <a href="{{ route('categories.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Zurück</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $category->name }} bearbeiten</h1>
        </div>
        <form method="POST" action="{{ route('categories.update', $category) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            @csrf @method('PUT')
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-800">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Farbe</label>
                <input type="color" name="color" value="{{ old('color', $category->color) }}" class="h-10 w-20 border border-gray-300 rounded-lg cursor-pointer">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
                <textarea name="description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('description', $category->description) }}</textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-4 py-2 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">Speichern</button>
                @if($category->materials_count == 0)
                <form method="POST" action="{{ route('categories.destroy', $category) }}" class="flex-1" onsubmit="return confirm('Löschen?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Löschen</button>
                </form>
                @endif
            </div>
        </form>
    </div>
</x-app-layout>
