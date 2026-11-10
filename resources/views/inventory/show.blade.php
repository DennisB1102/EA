<x-app-layout>
    @section('title', 'Bestand')
    <div class="max-w-lg">
        <a href="{{ route('inventory.index') }}" class="text-sm text-gray-500">← Zurück</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2 mb-4">Bestandseintrag</h1>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-sm space-y-3">
            <div class="flex justify-between"><span class="text-gray-500">Material</span><span>{{ $inventory->material->name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Standort</span><span>{{ $inventory->location->name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Menge</span><span>{{ $inventory->quantity }}</span></div>
            @if($inventory->expiry_date)<div class="flex justify-between"><span class="text-gray-500">Verfallsdatum</span><span>{{ $inventory->expiry_date->format('d.m.Y') }}</span></div>@endif
            @if($inventory->batch_number)<div class="flex justify-between"><span class="text-gray-500">Charge</span><span>{{ $inventory->batch_number }}</span></div>@endif
        </div>
    </div>
</x-app-layout>
