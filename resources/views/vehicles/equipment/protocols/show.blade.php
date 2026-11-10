<x-app-layout>
    @section('title', 'Protokoll vom ' . $protocol->checked_at->format('d.m.Y'))

    @php $issueCount = $protocol->items->where('is_ok', false)->count(); $items = $materialItems; @endphp

    {{-- ── Kopfzeile ────────────────────────────────────────────────── --}}
    <div class="mb-5 flex justify-between items-start">
        <div>
            <a href="{{ route('vehicles.equipment.protocols.index', [$vehicle, $equipment]) }}" class="text-sm text-gray-500">← Protokolle {{ $equipment->name }}</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">Prüfprotokoll</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $equipment->name }} · {{ $vehicle->name }}</p>
        </div>
        <a href="{{ route('vehicles.equipment.protocols.print', [$vehicle, $equipment, $protocol]) }}"
           target="_blank"
           class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm text-gray-600 hover:text-cd-red shrink-0">
            Drucken / PDF
        </a>
    </div>

    {{-- ── Status-Banner ─────────────────────────────────────────────── --}}
    @if($issueCount > 0)
    <div class="mb-5 flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm font-medium">
        <span class="text-xl">⚠</span>
        <span>Dieses Protokoll enthält <strong>{{ $issueCount }} {{ $issueCount === 1 ? 'Mangel' : 'Mängel' }}</strong> – bitte prüfen und beheben.</span>
    </div>
    @else
    <div class="mb-5 flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm font-medium">
        <span class="text-xl">✅</span>
        <span>Alle Positionen in Ordnung.</span>
    </div>
    @endif

    {{-- ── Zweispaltiges Layout ──────────────────────────────────────── --}}
    <div class="flex flex-col lg:flex-row gap-5 items-start">

        {{-- ── Linke Spalte: Materialien ────────────────────────────── --}}
        <div class="flex-1 min-w-0 space-y-4">
            @foreach($items as $category => $catItems)
            @php
                $catColor   = $catItems->first()->material->category->color ?? '#6B7280';
                $catIssues  = $catItems->where('is_ok', false)->count();
            @endphp
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                {{-- Kategorie-Header --}}
                <div class="flex items-center justify-between px-5 py-2.5 border-b border-black/10"
                     style="background-color: {{ $catColor }}">
                    <h3 class="font-semibold text-white text-sm">{{ $category }}</h3>
                    @if($catIssues > 0)
                        <span class="text-xs bg-white/20 text-white rounded-full px-2 py-0.5 font-medium">
                            {{ $catIssues }} ⚠
                        </span>
                    @else
                        <span class="text-xs text-white/70">{{ $catItems->count() }} Pos.</span>
                    @endif
                </div>

                {{-- Tabelle --}}
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-100">
                        <tr>
                            <th class="w-10 px-4 py-2 text-center text-xs font-medium text-gray-400"></th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Material</th>
                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-400 w-24">Ist / Soll</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Notiz</th>
                            <th class="w-24 px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($catItems as $item)
                        @php
                            $inv = $equipment->inventory->firstWhere('material_id', $item->material_id);
                        @endphp
                        <tr class="{{ !$item->is_ok ? ($item->isResolved() ? 'bg-green-50/40' : 'bg-red-50/50') : 'hover:bg-gray-50/50' }}">
                            <td class="px-4 py-2.5 text-center">
                                @if($item->is_ok)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-700 text-xs font-bold">✓</span>
                                @elseif($item->isResolved())
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-200 text-green-800 text-xs font-bold" title="Behoben">✓</span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-700 text-xs font-bold">✗</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="font-medium text-gray-900">{{ $item->material->name }}</div>
                                @if(!$item->is_ok && $item->isResolved())
                                    <div class="text-xs text-green-700 mt-0.5">Behoben von {{ $item->resolved_by }} · {{ $item->resolved_at->format('d.m.Y') }}</div>
                                    @if($item->resolution_notes)
                                        <div class="text-xs text-gray-500 italic">{{ $item->resolution_notes }}</div>
                                    @endif
                                @endif
                                @if($item->photo_path)
                                    <a href="{{ asset('storage/' . $item->photo_path) }}" target="_blank"
                                       class="inline-block mt-1">
                                        <img src="{{ asset('storage/' . $item->photo_path) }}"
                                             class="h-12 w-auto rounded border border-gray-200 object-cover" alt="Foto">
                                    </a>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                @if($item->current_quantity !== null)
                                    <span class="{{ $item->current_quantity < ($inv?->target_quantity ?? 0) ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                                        {{ $item->current_quantity }}
                                    </span>
                                    <span class="text-gray-300 mx-0.5">/</span>
                                    <span class="text-gray-400">{{ $inv?->target_quantity ?? '—' }}</span>
                                    <span class="text-gray-400 text-xs ml-0.5">{{ $item->material->unit }}</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-gray-500 italic text-xs">{{ $item->notes ?? '' }}</td>
                            <td class="px-4 py-2.5 text-right">
                                @if(!$item->is_ok && !$item->isResolved())
                                    <button type="button"
                                            onclick="openResolve({{ $item->id }}, '{{ route('vehicles.equipment.protocols.items.resolve', [$vehicle, $equipment, $protocol, $item]) }}')"
                                            class="text-xs px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 whitespace-nowrap">
                                        Behoben
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach

            {{-- ── Geräte ───────────────────────────────────────────── --}}
            @if($deviceItems->isNotEmpty())
            <div class="bg-white rounded-xl border border-blue-100 overflow-hidden">
                <div class="flex items-center justify-between px-5 py-2.5 border-b border-blue-100 bg-blue-600">
                    <h3 class="font-semibold text-white text-sm">Geräte</h3>
                    <span class="text-xs text-white/70">{{ $deviceItems->count() }} Gerät(e)</span>
                </div>
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-100">
                        <tr>
                            <th class="w-10 px-4 py-2 text-center text-xs font-medium text-gray-400"></th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Typ · Seriennummer</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Notiz</th>
                            <th class="w-24 px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($deviceItems as $item)
                        <tr class="{{ !$item->is_ok ? ($item->isResolved() ? 'bg-green-50/40' : 'bg-red-50/50') : '' }}">
                            <td class="px-4 py-2.5 text-center">
                                @if($item->is_ok)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-100 text-green-700 text-xs font-bold">✓</span>
                                @elseif($item->isResolved())
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-green-200 text-green-800 text-xs font-bold" title="Behoben">✓</span>
                                @else
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-red-100 text-red-700 text-xs font-bold">✗</span>
                                @endif
                            </td>
                            <td class="px-4 py-2.5">
                                <div>
                                    <span class="font-medium text-gray-900">{{ $item->deviceInstance->material->name }}</span>
                                    <span class="ml-2 font-mono text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">
                                        {{ $item->deviceInstance->serial_number ?: 'Keine SN' }}
                                    </span>
                                </div>
                                @if(!$item->is_ok && $item->isResolved())
                                    <div class="text-xs text-green-700 mt-0.5">Behoben von {{ $item->resolved_by }} · {{ $item->resolved_at->format('d.m.Y') }}</div>
                                @endif
                                @if($item->photo_path)
                                    <a href="{{ asset('storage/' . $item->photo_path) }}" target="_blank"
                                       class="inline-block mt-1">
                                        <img src="{{ asset('storage/' . $item->photo_path) }}"
                                             class="h-12 w-auto rounded border border-gray-200 object-cover" alt="Foto">
                                    </a>
                                @endif
                            </td>
                            <td class="px-4 py-2.5 text-gray-500 italic text-xs">{{ $item->notes ?? '' }}</td>
                            <td class="px-4 py-2.5 text-right">
                                @if(!$item->is_ok && !$item->isResolved())
                                    <button type="button"
                                            onclick="openResolve({{ $item->id }}, '{{ route('vehicles.equipment.protocols.items.resolve', [$vehicle, $equipment, $protocol, $item]) }}')"
                                            class="text-xs px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 whitespace-nowrap">
                                        Behoben
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- ── Rechte Sidebar ───────────────────────────────────────── --}}
        <div class="w-full lg:w-72 shrink-0 space-y-4 lg:sticky lg:top-6">

            {{-- Metadaten --}}
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Prüfungsdetails</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    <div class="px-5 py-3">
                        <p class="text-xs text-gray-400 mb-0.5">Datum</p>
                        <p class="font-semibold text-gray-900">{{ $protocol->checked_at->format('d.m.Y') }}</p>
                    </div>
                    <div class="px-5 py-3">
                        <p class="text-xs text-gray-400 mb-0.5">Geprüft durch</p>
                        <p class="font-semibold text-gray-900">{{ $protocol->checked_by }}</p>
                    </div>
                    <div class="px-5 py-3 flex justify-between items-center">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Positionen</p>
                            <p class="font-semibold text-gray-900">{{ $protocol->items->count() }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-400 mb-0.5">Mängel</p>
                            <p class="font-semibold {{ $issueCount > 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ $issueCount }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Allgemeine Notizen --}}
            @if($protocol->notes)
            <div class="bg-yellow-50 border border-yellow-100 rounded-xl overflow-hidden">
                <div class="px-5 py-3 border-b border-yellow-100">
                    <h2 class="text-xs font-semibold text-yellow-700 uppercase tracking-wider">Notizen</h2>
                </div>
                <p class="px-5 py-3 text-sm text-gray-700">{{ $protocol->notes }}</p>
            </div>
            @endif

            {{-- Unterschrift --}}
            @if($protocol->signature_data)
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Unterschrift</h2>
                </div>
                <div class="p-4">
                    <div class="border border-gray-100 rounded-lg overflow-hidden bg-gray-50">
                        <img src="{{ $protocol->signature_data }}" alt="Unterschrift" class="w-full h-24 object-contain block">
                    </div>
                    <p class="text-xs text-gray-400 mt-2">{{ $protocol->checked_by }} · {{ $protocol->checked_at->format('d.m.Y') }}</p>
                </div>
            </div>
            @endif

            {{-- Kategorie-Schnellübersicht --}}
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategorien</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach($items as $category => $catItems)
                    @php
                        $catColor  = $catItems->first()->material->category->color ?? '#6B7280';
                        $catIssues = $catItems->where('is_ok', false)->count();
                    @endphp
                    <div class="px-5 py-2.5 flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $catColor }}"></span>
                        <span class="text-sm text-gray-700 flex-1 truncate">{{ $category }}</span>
                        @if($catIssues > 0)
                            <span class="text-xs text-red-600 font-semibold shrink-0">{{ $catIssues }} ⚠</span>
                        @else
                            <span class="text-xs text-green-500 shrink-0">✓</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    {{-- ── Resolve-Modal ──────────────────────────────────────────────── --}}
    <div id="resolve-modal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Mangel als behoben markieren</h3>
            </div>
            <form id="resolve-form" method="POST">
                @csrf @method('PATCH')
                <div class="p-5 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Behoben durch <span class="text-red-500">*</span></label>
                        <input type="text" name="resolved_by" required placeholder="Name"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Bemerkung</label>
                        <textarea name="resolution_notes" rows="2" placeholder="Optional…"
                                  class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500/30 resize-none"></textarea>
                    </div>
                </div>
                <div class="px-5 pb-5 flex gap-2">
                    <button type="submit" class="flex-1 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700">Speichern</button>
                    <button type="button" onclick="closeResolve()" class="flex-1 py-2 border border-gray-200 text-gray-600 rounded-lg text-sm hover:border-gray-300">Abbrechen</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openResolve(itemId, url) {
        document.getElementById('resolve-form').action = url;
        document.getElementById('resolve-modal').classList.remove('hidden');
    }
    function closeResolve() {
        document.getElementById('resolve-modal').classList.add('hidden');
    }
    document.getElementById('resolve-modal').addEventListener('click', function(e) {
        if (e.target === this) closeResolve();
    });
    </script>
</x-app-layout>
