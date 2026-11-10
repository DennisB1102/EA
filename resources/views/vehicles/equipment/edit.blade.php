<x-app-layout>
    @section('title', 'Ausstattung bearbeiten')
    <div class="mb-6">
        <a href="{{ route('vehicles.equipment.show', [$vehicle, $equipment]) }}" class="text-sm text-gray-500">← {{ $equipment->name }}</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Ausstattung bearbeiten</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-xl">
        <form method="POST" action="{{ route('vehicles.equipment.update', [$vehicle, $equipment]) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="type" value="Typ" />
                <select id="type" name="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cd-red focus:border-cd-red text-sm">
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ old('type', $equipment->type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('type')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="name" value="Bezeichnung" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $equipment->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="description" value="Beschreibung" />
                <textarea id="description" name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cd-red focus:border-cd-red text-sm">{{ old('description', $equipment->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-1" />
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1" class="rounded border-gray-300 text-cd-red focus:ring-cd-red" {{ $equipment->is_active ? 'checked' : '' }}>
                <x-input-label for="is_active" value="Aktiv" class="mb-0" />
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('vehicles.equipment.show', [$vehicle, $equipment]) }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Abbrechen</a>
                <x-primary-button>Speichern</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
