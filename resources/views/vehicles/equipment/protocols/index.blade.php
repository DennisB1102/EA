<x-app-layout>
    @section('title', 'Protokolle – ' . $equipment->name)

    <div class="mb-6 flex justify-between items-start">
        <div>
            <a href="{{ route('vehicles.equipment.show', [$vehicle, $equipment]) }}" class="text-sm text-gray-500">← {{ $equipment->name }}</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">Prüfprotokolle</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $equipment->name }} · {{ $vehicle->name }}</p>
        </div>
        <a href="{{ route('vehicles.equipment.protocols.create', [$vehicle, $equipment]) }}"
           class="px-4 py-2 bg-cd-red text-white rounded-lg text-sm font-semibold hover:bg-[#b5010f]">
            + Neues Protokoll
        </a>
    </div>

    @if($protocols->isEmpty())
        <div class="bg-white rounded-xl border border-gray-100 p-10 text-center text-gray-400">
            Noch kein Prüfprotokoll vorhanden.
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 font-medium text-gray-600">Datum</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600">Geprüft durch</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600">Positionen</th>
                        <th class="text-left px-5 py-3 font-medium text-gray-600">Mängel</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($protocols as $protocol)
                    @php $issues = $protocol->items->where('is_ok', false)->count(); @endphp
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 font-medium text-gray-900">
                            {{ $protocol->checked_at->format('d.m.Y') }}
                        </td>
                        <td class="px-5 py-3 text-gray-700">{{ $protocol->checked_by }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $protocol->items->count() }}</td>
                        <td class="px-5 py-3">
                            @if($issues > 0)
                                <span class="text-red-600 font-semibold">{{ $issues }} ⚠</span>
                            @else
                                <span class="text-green-600">✓ Keine</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('vehicles.equipment.protocols.show', [$vehicle, $equipment, $protocol]) }}"
                               class="text-xs text-gray-500 hover:text-cd-red">Anzeigen</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-app-layout>
