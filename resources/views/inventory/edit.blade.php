<x-app-layout>
    @section('title', 'Bestand bearbeiten')
    <div class="max-w-lg">
        <div class="mb-6">
            <a href="{{ route('inventory.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Zurück</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Bestand bearbeiten</h1>
            <p class="text-gray-500 text-sm">{{ $inventory->material->name }} @ {{ $inventory->location->name }}</p>
        </div>
        <form method="POST" action="{{ route('inventory.update', $inventory) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Menge *</label>
                <input type="number" name="quantity" value="{{ old('quantity', $inventory->quantity) }}" min="0"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Verfallsdatum</label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date', $inventory->expiry_date?->format('Y-m-d')) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chargennummer</label>
                    <input type="text" name="batch_number" value="{{ old('batch_number', $inventory->batch_number) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notiz</label>
                <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('notes', $inventory->notes) }}</textarea>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">Speichern</button>
        </form>
    </div>
</x-app-layout>
