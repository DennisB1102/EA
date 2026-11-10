<x-app-layout>
    @section('title', 'Fahrzeug anlegen')
    <div class="mb-6">
        <a href="{{ route('vehicles.index') }}" class="text-sm text-gray-500">← Zurück</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Fahrzeug anlegen</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-xl">
        <form method="POST" action="{{ route('vehicles.store') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="location_id" value="Wache" />
                <select id="location_id" name="location_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cd-red focus:border-cd-red text-sm">
                    <option value="">– Wache wählen –</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('location_id')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="name" value="Bezeichnung" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="call_sign" value="Funkrufname" />
                <x-text-input id="call_sign" name="call_sign" type="text" class="mt-1 block w-full font-mono" :value="old('call_sign')" placeholder="z. B. Florian Muster 1-40-1" />
                <x-input-error :messages="$errors->get('call_sign')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="license_plate" value="Kennzeichen" />
                <x-text-input id="license_plate" name="license_plate" type="text" class="mt-1 block w-full" :value="old('license_plate')" />
                <x-input-error :messages="$errors->get('license_plate')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="tuev_date" value="TÜV fällig am" />
                <x-text-input id="tuev_date" name="tuev_date" type="date" class="mt-1 block w-full" :value="old('tuev_date')" />
                <x-input-error :messages="$errors->get('tuev_date')" class="mt-1" />
            </div>

            <div>
                <x-input-label for="notes" value="Bemerkungen" />
                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-cd-red focus:border-cd-red text-sm">{{ old('notes') }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-1" />
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('vehicles.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Abbrechen</a>
                <x-primary-button>Speichern</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
