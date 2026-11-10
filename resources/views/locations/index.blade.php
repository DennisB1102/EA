<x-app-layout>
    @section('title', 'Standorte')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Standorte</h1>
        <a href="{{ route('locations.create') }}" class="px-4 py-2 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">+ Neu</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($locations as $loc)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-semibold text-gray-900">{{ $loc->name }}</h3>
                @if(!$loc->is_active)
                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded">Inaktiv</span>
                @endif
            </div>
            @if($loc->address)
            <p class="text-sm text-gray-400 mb-2">{{ $loc->address }}</p>
            @endif
            <p class="text-sm text-gray-500">{{ $loc->inventory_count }} Bestandspositionen</p>
            <div class="mt-3 pt-3 border-t border-gray-50 flex gap-3">
                <a href="{{ route('locations.show', $loc) }}" class="text-xs text-gray-500 hover:text-cd-red">Anzeigen</a>
                <a href="{{ route('locations.edit', $loc) }}" class="text-xs text-gray-500 hover:text-cd-red">Bearbeiten</a>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center text-gray-400 py-8">Keine Standorte vorhanden.</div>
        @endforelse
    </div>
</x-app-layout>
