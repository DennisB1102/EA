<x-app-layout>
    @section('title', 'Beladung bearbeiten')
    <div class="mb-6">
        <a href="{{ route('vehicles.equipment.show', [$vehicle, $equipment]) }}" class="text-sm text-gray-500">← {{ $equipment->name }}</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Beladung bearbeiten</h1>
        <p class="text-sm text-gray-400 mt-0.5">{{ $vehicle->name }} → {{ $equipment->name }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-xl">
        <form method="POST" action="{{ route('vehicles.equipment.inventory.update', [$vehicle, $equipment, $inventory]) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="material_id" value="Material" />
                <select id="material_id" name="material_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cd-red focus:border-cd-red text-sm">
                    @foreach($materials as $mat)
                        <option value="{{ $mat->id }}" {{ old('material_id', $inventory->material_id) == $mat->id ? 'selected' : '' }}>
                            {{ $mat->name }} ({{ $mat->category->name }})
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('material_id')" class="mt-1" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-input-label for="target_quantity" value="Soll-Menge" />
                    <x-text-input id="target_quantity" name="target_quantity" type="number" min="1" class="mt-1 block w-full" :value="old('target_quantity', $inventory->target_quantity)" required />
                    <x-input-error :messages="$errors->get('target_quantity')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="current_quantity" value="Ist-Menge" />
                    <x-text-input id="current_quantity" name="current_quantity" type="number" min="0" class="mt-1 block w-full" :value="old('current_quantity', $inventory->current_quantity)" required />
                    <x-input-error :messages="$errors->get('current_quantity')" class="mt-1" />
                </div>
            </div>

            <div>
                <x-input-label for="expiry_date" value="Verfallsdatum" />
                <x-text-input id="expiry_date" name="expiry_date" type="date" class="mt-1 block w-full" :value="old('expiry_date', $inventory->expiry_date?->format('Y-m-d'))" />
                <x-input-error :messages="$errors->get('expiry_date')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="notes" value="Bemerkungen" />
                <textarea id="notes" name="notes" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cd-red focus:border-cd-red text-sm">{{ old('notes', $inventory->notes) }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-1" />
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('vehicles.equipment.show', [$vehicle, $equipment]) }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Abbrechen</a>
                <x-primary-button>Speichern</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
