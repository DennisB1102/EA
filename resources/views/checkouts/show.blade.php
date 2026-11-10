<x-app-layout>
    @section('title', 'Entnahme')
    <div class="max-w-lg">
        <a href="{{ route('checkouts.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Zurück</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2 mb-4">Entnahme #{{ $checkout->id }}</h1>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-sm space-y-3">
            <div class="flex justify-between"><span class="text-gray-500">Material</span><span>{{ $checkout->material->name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Standort</span><span>{{ $checkout->location->name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Menge</span><span>{{ $checkout->quantity }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Benutzer</span><span>{{ $checkout->user->name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Datum</span><span>{{ $checkout->created_at->format('d.m.Y H:i') }}</span></div>
            @if($checkout->purpose)<div class="flex justify-between"><span class="text-gray-500">Zweck</span><span>{{ $checkout->purpose }}</span></div>@endif
            @if($checkout->notes)<div class="flex justify-between"><span class="text-gray-500">Notiz</span><span>{{ $checkout->notes }}</span></div>@endif
            <div class="flex justify-between"><span class="text-gray-500">Status</span>
                <span class="px-2 py-0.5 rounded text-xs {{ $checkout->isReturned() ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $checkout->isReturned() ? 'Zurückgegeben' : 'Offen' }}
                </span>
            </div>
        </div>
    </div>
</x-app-layout>
