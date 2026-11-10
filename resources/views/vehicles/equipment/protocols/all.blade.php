<x-app-layout>
    @section('title', 'NFR Prüfprotokolle')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">NFR Prüfprotokolle</h1>
        <p class="text-sm text-gray-500 mt-0.5">Alle Notfallrucksäcke im Überblick</p>
    </div>

    {{-- Übersicht: letzter Check je NFR --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-8">
        @foreach($nfrs as $nfr)
        @php
            $last = $protocols->get($nfr->name)?->first();
            $issues = $last?->items->where('is_ok', false)->count() ?? 0;
        @endphp
        <a href="{{ route('vehicles.equipment.protocols.index', [$nfr->vehicle, $nfr]) }}"
           class="bg-white rounded-xl border border-gray-100 p-4 hover:border-cd-red/30 transition-colors">
            <p class="font-bold text-gray-900 text-sm">{{ $nfr->name }}</p>
            @if($last)
                <p class="text-xs text-gray-400 mt-1">{{ $last->checked_at->format('d.m.Y') }}</p>
                @if($issues > 0)
                    <span class="inline-block mt-2 text-xs font-medium text-red-600 bg-red-50 rounded px-1.5 py-0.5">{{ $issues }} Mangel</span>
                @else
                    <span class="inline-block mt-2 text-xs font-medium text-green-600 bg-green-50 rounded px-1.5 py-0.5">✓ i.O.</span>
                @endif
            @else
                <p class="text-xs text-gray-400 mt-1">Noch kein Check</p>
                <span class="inline-block mt-2 text-xs font-medium text-gray-400 bg-gray-50 rounded px-1.5 py-0.5">—</span>
            @endif
        </a>
        @endforeach
    </div>

    {{-- Protokoll-Liste je NFR --}}
    @forelse($protocols as $nfrName => $nfrProtocols)
    @php $eq = $nfrProtocols->first()->equipment; @endphp
    <div class="mb-6">
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 border-b border-black/10" style="background-color: #EA580C">
            <h2 class="text-sm font-semibold text-white uppercase tracking-wider">{{ $nfrName }}</h2>
            <a href="{{ route('vehicles.equipment.protocols.create', [$eq->vehicle, $eq]) }}"
               class="text-xs text-white/80 hover:text-white">+ Neues Protokoll</a>
        </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-2.5 font-medium text-gray-500 text-xs">Datum</th>
                        <th class="text-left px-5 py-2.5 font-medium text-gray-500 text-xs">Geprüft durch</th>
                        <th class="text-left px-5 py-2.5 font-medium text-gray-500 text-xs">Mängel</th>
                        <th class="px-5 py-2.5"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($nfrProtocols as $protocol)
                    @php $issues = $protocol->items->where('is_ok', false)->count(); @endphp
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-2.5 font-medium text-gray-900">{{ $protocol->checked_at->format('d.m.Y') }}</td>
                        <td class="px-5 py-2.5 text-gray-600">{{ $protocol->checked_by }}</td>
                        <td class="px-5 py-2.5">
                            @if($issues > 0)
                                <span class="text-red-600 font-semibold">{{ $issues }} ⚠</span>
                            @else
                                <span class="text-green-600 text-xs">✓ Keine</span>
                            @endif
                        </td>
                        <td class="px-5 py-2.5 text-right">
                            <a href="{{ route('vehicles.equipment.protocols.show', [$protocol->equipment->vehicle, $protocol->equipment, $protocol]) }}"
                               class="text-xs text-gray-400 hover:text-cd-red">Anzeigen</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-gray-100 p-10 text-center text-gray-400">
        Noch keine Prüfprotokolle vorhanden.
    </div>
    @endforelse
</x-app-layout>
