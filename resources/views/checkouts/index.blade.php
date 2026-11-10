<x-app-layout>
    @section('title', 'Entnahmen')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Entnahmen</h1>
        <a href="{{ route('checkouts.create') }}" class="px-4 py-2 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">+ Neue Entnahme</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-4 p-4">
        <form method="GET" class="flex gap-3">
            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="open" value="1" {{ request('open') ? 'checked' : '' }} class="rounded">
                Nur offene Entnahmen
            </label>
            <button class="px-4 py-1.5 bg-gray-700 text-white rounded-lg text-sm">Filtern</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Material</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Standort</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Menge</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Benutzer</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Datum</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($checkouts as $c)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $c->material->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $c->location->name }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $c->quantity }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $c->user->name }}</td>
                    <td class="px-4 py-3 text-gray-400">{{ $c->created_at->format('d.m.Y H:i') }}</td>
                    <td class="px-4 py-3">
                        @if($c->isReturned())
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Zurückgegeben</span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">Offen</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if(!$c->isReturned())
                        <form method="POST" action="{{ route('checkouts.return', $c) }}" class="inline">
                            @csrf @method('PATCH')
                            <button class="text-xs text-cd-red hover:underline">Rückgabe</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Keine Entnahmen gefunden.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3">{{ $checkouts->links() }}</div>
    </div>
</x-app-layout>
