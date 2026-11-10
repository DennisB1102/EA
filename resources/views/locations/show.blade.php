<x-app-layout>
    @section('title', $location->name)
    <div class="mb-6">
        <a href="{{ route('locations.index') }}" class="text-sm text-gray-500">← Zurück</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $location->name }}</h1>
        @if($location->address)<p class="text-gray-400 text-sm">{{ $location->address }}</p>@endif
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Material</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Kategorie</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Menge</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Verfallsdatum</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($location->inventory as $inv)
                <tr>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $inv->material->name }}</td>
                    <td class="px-4 py-3 text-gray-500">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full" style="background:{{ $inv->material->category->color }}"></span>
                            {{ $inv->material->category->name }}
                        </span>
                    </td>
                    <td class="px-4 py-3">{{ $inv->quantity }} {{ $inv->material->unit }}</td>
                    <td class="px-4 py-3 text-gray-400">{{ $inv->expiry_date?->format('d.m.Y') ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Kein Bestand an diesem Standort.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
