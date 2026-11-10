<x-app-layout>
    @section('title', 'Bestand')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Bestand</h1>
        <a href="{{ route('inventory.create') }}" class="px-4 py-2 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">+ Bestand buchen</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-4 p-4">
        <form method="GET" class="flex gap-3 flex-wrap">
            <select name="location" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Alle Standorte</option>
                @foreach($locations as $l)
                <option value="{{ $l->id }}" {{ request('location') == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                @endforeach
            </select>
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="expiring" value="1" {{ request('expiring') ? 'checked' : '' }} class="rounded">
                Bald ablaufend
            </label>
            <button class="px-4 py-2 bg-gray-700 text-white rounded-lg text-sm">Filtern</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Material</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Kategorie</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Standort</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Menge</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Verfallsdatum</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($inventory as $inv)
                <tr class="hover:bg-gray-50 {{ $inv->isExpired() ? 'bg-red-50' : '' }}">
                    <td class="px-4 py-3">
                        <a href="{{ route('materials.show', $inv->material) }}" class="font-medium text-gray-900 hover:text-cd-red">{{ $inv->material->name }}</a>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5 text-gray-600">
                            <span class="w-2 h-2 rounded-full" style="background:{{ $inv->material->category->color }}"></span>
                            {{ $inv->material->category->name }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $inv->location->name }}</td>
                    <td class="px-4 py-3 font-medium">{{ $inv->quantity }} {{ $inv->material->unit }}</td>
                    <td class="px-4 py-3">
                        @if($inv->expiry_date)
                        <span class="{{ $inv->isExpired() ? 'text-red-600 font-semibold' : ($inv->isExpiringSoon() ? 'text-orange-600' : 'text-gray-500') }}">
                            {{ $inv->expiry_date->format('d.m.Y') }}
                        </span>
                        @else
                        <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('inventory.edit', $inv) }}" class="text-xs text-gray-400 hover:text-cd-red">Bearbeiten</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Kein Bestand vorhanden.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3">{{ $inventory->links() }}</div>
    </div>
</x-app-layout>
