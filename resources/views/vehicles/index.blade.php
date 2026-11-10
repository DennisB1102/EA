<x-app-layout>
    @section('title', 'Fahrzeuge')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Fahrzeuge</h1>
        <a href="{{ route('vehicles.create') }}" class="px-4 py-2 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">+ Neu</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($vehicles as $vehicle)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            @if($vehicle->image_path)
                <div class="mb-3 -mx-5 -mt-5 px-5 pt-4 pb-3 bg-gray-50 rounded-t-xl flex justify-center border-b border-gray-100">
                    <img src="{{ asset('storage/' . $vehicle->image_path) }}" alt="{{ $vehicle->name }}" class="max-h-24 object-contain">
                </div>
            @endif
            <div class="flex justify-between items-start mb-1">
                <h3 class="font-semibold text-gray-900">{{ $vehicle->name }}</h3>
                @if(!$vehicle->is_active)
                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded">Inaktiv</span>
                @endif
            </div>
            @if($vehicle->call_sign)
                <p class="text-sm text-gray-500 font-mono">{{ $vehicle->call_sign }}</p>
            @endif
            @if($vehicle->license_plate)
                <p class="text-xs text-gray-400 mt-0.5">{{ $vehicle->license_plate }}</p>
            @endif
            <p class="text-xs text-gray-400 mt-1">{{ $vehicle->location->name }}</p>

            @if($vehicle->tuev_date)
                <div class="mt-2">
                    @if($vehicle->isTuevExpired())
                        <span class="inline-flex items-center gap-1 text-xs bg-red-50 text-red-700 px-2 py-0.5 rounded font-medium">
                            TÜV abgelaufen: {{ $vehicle->tuev_date->format('d.m.Y') }}
                        </span>
                    @elseif($vehicle->isTuevExpiringSoon())
                        <span class="inline-flex items-center gap-1 text-xs bg-yellow-50 text-yellow-700 px-2 py-0.5 rounded font-medium">
                            TÜV bald: {{ $vehicle->tuev_date->format('d.m.Y') }}
                        </span>
                    @else
                        <span class="text-xs text-gray-400">TÜV: {{ $vehicle->tuev_date->format('d.m.Y') }}</span>
                    @endif
                </div>
            @endif

            <p class="text-sm text-gray-500 mt-2">{{ $vehicle->equipment_count }} Ausstattung</p>

            <div class="mt-3 pt-3 border-t border-gray-50 flex gap-3">
                <a href="{{ route('vehicles.show', $vehicle) }}" class="text-xs text-gray-500 hover:text-cd-red">Anzeigen</a>
                <a href="{{ route('vehicles.edit', $vehicle) }}" class="text-xs text-gray-500 hover:text-cd-red">Bearbeiten</a>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center text-gray-400 py-8">Keine Fahrzeuge vorhanden.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $vehicles->links() }}</div>
</x-app-layout>
