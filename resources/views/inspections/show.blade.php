<x-app-layout>
    @section('title', 'Prüfung')
    <div class="max-w-lg">
        <a href="{{ route('inspections.index') }}" class="text-sm text-gray-500">← Zurück</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2 mb-4">Prüfung #{{ $inspection->id }}</h1>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-sm space-y-3">
            <div class="flex justify-between"><span class="text-gray-500">Material</span><span>{{ $inspection->material->name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Standort</span><span>{{ $inspection->location->name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Ergebnis</span><span>{{ $inspection->result }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Prüfer</span><span>{{ $inspection->user->name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Datum</span><span>{{ $inspection->created_at->format('d.m.Y') }}</span></div>
            @if($inspection->notes)<div class="flex justify-between"><span class="text-gray-500">Notiz</span><span>{{ $inspection->notes }}</span></div>@endif
        </div>
    </div>
</x-app-layout>
