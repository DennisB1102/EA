<x-app-layout>
    @section('title', 'Ausstattung hinzufügen')
    <div class="mb-6">
        <a href="{{ route('vehicles.show', $vehicle) }}" class="text-sm text-gray-500">← {{ $vehicle->name }}</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Ausstattung hinzufügen</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-xl">
        <form method="POST" action="{{ route('vehicles.equipment.store', $vehicle) }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="type" value="Typ" />
                <select id="type" name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cd-red focus:border-cd-red text-sm">
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('type')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="name" value="Bezeichnung" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" placeholder="z. B. Notfallrucksack A" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="description" value="Beschreibung" />
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cd-red focus:border-cd-red text-sm">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-1" />
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('vehicles.show', $vehicle) }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Abbrechen</a>
                <x-primary-button>Speichern</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
