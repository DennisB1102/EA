@use('Illuminate\Support\Facades\Storage')
<x-app-layout>
    @section('title', 'Fahrzeug bearbeiten')
    <div class="mb-6">
        <a href="{{ route('vehicles.show', $vehicle) }}" class="text-sm text-gray-500">← Zurück</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $vehicle->name }} bearbeiten</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-xl">
        <form method="POST" action="{{ route('vehicles.update', $vehicle) }}" class="space-y-4" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="location_id" value="Wache" />
                <select id="location_id" name="location_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cd-red focus:border-cd-red text-sm">
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('location_id', $vehicle->location_id) == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('location_id')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="name" value="Bezeichnung" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $vehicle->name)" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="call_sign" value="Funkrufname" />
                <x-text-input id="call_sign" name="call_sign" type="text" class="mt-1 block w-full font-mono" :value="old('call_sign', $vehicle->call_sign)" />
                <x-input-error :messages="$errors->get('call_sign')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="license_plate" value="Kennzeichen" />
                <x-text-input id="license_plate" name="license_plate" type="text" class="mt-1 block w-full" :value="old('license_plate', $vehicle->license_plate)" />
                <x-input-error :messages="$errors->get('license_plate')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="tuev_date" value="TÜV fällig am" />
                <x-text-input id="tuev_date" name="tuev_date" type="date" class="mt-1 block w-full" :value="old('tuev_date', $vehicle->tuev_date?->format('Y-m-d'))" />
                <x-input-error :messages="$errors->get('tuev_date')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="notes" value="Bemerkungen" />
                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cd-red focus:border-cd-red text-sm">{{ old('notes', $vehicle->notes) }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="image" value="Fahrzeugbild" />
                @if($vehicle->image_path)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $vehicle->image_path) }}" alt="{{ $vehicle->name }}" class="max-h-32 object-contain rounded border border-gray-100">
                    </div>
                @endif
                <input type="file" id="image" name="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-sm file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                <x-input-error :messages="$errors->get('image')" class="mt-1" />
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1" class="rounded border-gray-300 text-cd-red focus:ring-cd-red" {{ $vehicle->is_active ? 'checked' : '' }}>
                <x-input-label for="is_active" value="Aktiv" class="mb-0" />
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('vehicles.show', $vehicle) }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Abbrechen</a>
                <x-primary-button>Speichern</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
