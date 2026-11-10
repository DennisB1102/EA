<x-app-layout>
    @section('title', $material->name)
    <div class="mb-6 flex justify-between items-start">
        <div>
            <a href="{{ route('materials.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Zurück</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $material->name }}</h1>
            <div class="flex items-center gap-2 mt-1">
                <span class="w-2.5 h-2.5 rounded-full" style="background:{{ $material->category->color }}"></span>
                <span class="text-sm text-gray-500">{{ $material->category->name }}</span>
                @if($material->article_number)
                <span class="text-gray-300">·</span>
                <span class="text-sm text-gray-400">{{ $material->article_number }}</span>
                @endif
            </div>
        </div>
        <a href="{{ route('materials.edit', $material) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Bearbeiten</a>
    </div>

    @if($material->is_device)
    {{-- ── Geräte-Instanzen ──────────────────────────────────────────────────── --}}
    <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h2 class="font-semibold text-gray-800">Geräte <span class="text-gray-400 font-normal text-sm">({{ $material->deviceInstances->where('is_active', true)->count() }})</span></h2>
            <button onclick="document.getElementById('add-device-form').classList.toggle('hidden')"
                    class="text-sm text-cd-red hover:underline">+ Gerät hinzufügen</button>
        </div>

        {{-- Neues Gerät --}}
        <div id="add-device-form" class="hidden border-b border-gray-100 p-5 bg-blue-50/40">
            <form method="POST" action="{{ route('materials.devices.store', $material) }}">
                @csrf
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3">
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Seriennummer</label>
                        <input type="text" name="serial_number" placeholder="SN-12345"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cd-red/30">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">MTK Ablauf</label>
                        <input type="date" name="mtk_ablauf"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cd-red/30">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">STK Ablauf</label>
                        <input type="date" name="stk_ablauf"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cd-red/30">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Notiz</label>
                        <input type="text" name="notes" placeholder="Optional"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cd-red/30">
                    </div>
                </div>
                <button type="submit" class="px-4 py-2 bg-cd-red text-white text-sm font-medium rounded-lg hover:bg-[#b5010f]">
                    Speichern
                </button>
            </form>
        </div>

        {{-- Liste --}}
        @php
            $instances = $material->deviceInstances->where('is_active', true)->sortBy('serial_number');
        @endphp
        @if($instances->isEmpty())
            <p class="px-5 py-4 text-sm text-gray-400">Noch keine Geräte hinterlegt.</p>
        @else
        <table class="w-full text-sm">
            <thead class="border-b border-gray-100 bg-gray-50/50">
                <tr>
                    <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Seriennummer</th>
                    <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">MTK Ablauf</th>
                    <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">STK Ablauf</th>
                    <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Notiz</th>
                    <th class="px-5 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($instances as $inst)
                @php
                    $mtkStatus = $inst->mtkStatus();
                    $stkStatus = $inst->stkStatus();
                @endphp
                <tr class="hover:bg-gray-50/50 group" x-data="{ editing: false }">
                    {{-- Ansichts-Zeile --}}
                    <td class="px-5 py-3 font-mono text-gray-800" x-show="!editing">
                        {{ $inst->serial_number ?: '—' }}
                    </td>
                    <td class="px-5 py-3" x-show="!editing">
                        @if($inst->mtk_ablauf)
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2 py-0.5 rounded-full
                                {{ $mtkStatus === 'expired' ? 'bg-red-100 text-red-700' : ($mtkStatus === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                {{ $mtkStatus === 'expired' ? '✗' : ($mtkStatus === 'warning' ? '⚠' : '✓') }}
                                {{ $inst->mtk_ablauf->format('d.m.Y') }}
                            </span>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3" x-show="!editing">
                        @if($inst->stk_ablauf)
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2 py-0.5 rounded-full
                                {{ $stkStatus === 'expired' ? 'bg-red-100 text-red-700' : ($stkStatus === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                {{ $stkStatus === 'expired' ? '✗' : ($stkStatus === 'warning' ? '⚠' : '✓') }}
                                {{ $inst->stk_ablauf->format('d.m.Y') }}
                            </span>
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-500 text-xs" x-show="!editing">{{ $inst->notes }}</td>
                    <td class="px-5 py-3 text-right" x-show="!editing">
                        <button @click="editing = true" class="text-xs text-gray-400 hover:text-cd-red opacity-0 group-hover:opacity-100">
                            Bearbeiten
                        </button>
                    </td>

                    {{-- Bearbeitungs-Zeile --}}
                    <td colspan="5" class="px-5 py-3" x-show="editing" x-cloak>
                        <form method="POST" action="{{ route('materials.devices.update', [$material, $inst]) }}" class="grid grid-cols-2 md:grid-cols-5 gap-2 items-end">
                            @csrf @method('PATCH')
                            <input type="text" name="serial_number" value="{{ $inst->serial_number }}" placeholder="Seriennummer"
                                   class="border border-gray-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-cd-red/30">
                            <input type="date" name="mtk_ablauf" value="{{ $inst->mtk_ablauf?->format('Y-m-d') }}"
                                   class="border border-gray-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-cd-red/30">
                            <input type="date" name="stk_ablauf" value="{{ $inst->stk_ablauf?->format('Y-m-d') }}"
                                   class="border border-gray-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-cd-red/30">
                            <input type="text" name="notes" value="{{ $inst->notes }}" placeholder="Notiz"
                                   class="border border-gray-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-cd-red/30">
                            <div class="flex gap-1.5">
                                <button type="submit" class="px-3 py-1.5 bg-cd-red text-white text-xs rounded-lg hover:bg-[#b5010f]">Speichern</button>
                                <button type="button" @click="editing = false" class="px-3 py-1.5 border border-gray-200 text-gray-600 text-xs rounded-lg">Abbrechen</button>
                                <form method="POST" action="{{ route('materials.devices.destroy', [$material, $inst]) }}" class="inline"
                                      onsubmit="return confirm('Gerät deaktivieren?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 border border-red-200 text-red-500 text-xs rounded-lg hover:bg-red-50">
                                        Entfernen
                                    </button>
                                </form>
                            </div>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Bestand --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="font-semibold text-gray-800">Bestand nach Standort</h2>
                    <a href="{{ route('inventory.create') }}?material_id={{ $material->id }}" class="text-sm text-cd-red hover:underline">+ Bestand buchen</a>
                </div>
                @forelse($material->inventory as $inv)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 text-sm last:border-0">
                    <span class="text-gray-700">{{ $inv->location->name }}</span>
                    <span class="font-medium {{ $inv->isExpired() ? 'text-red-600' : '' }}">{{ $inv->quantity }} {{ $material->unit }}</span>
                    @if($inv->expiry_date)
                    <span class="text-xs {{ $inv->isExpired() ? 'text-red-500' : 'text-gray-400' }}">MHD: {{ $inv->expiry_date->format('d.m.Y') }}</span>
                    @endif
                </div>
                @empty
                <p class="text-gray-400 text-sm">Kein Bestand vorhanden.</p>
                @endforelse
                <div class="mt-3 pt-3 border-t border-gray-100 flex justify-between text-sm font-semibold">
                    <span>Gesamtbestand</span>
                    <span class="{{ $material->isBelowMinStock() && $material->min_stock > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $material->totalStock() }} {{ $material->unit }}</span>
                </div>
            </div>

            {{-- Letzte Entnahmen --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="font-semibold text-gray-800 mb-3">Letzte Entnahmen</h2>
                @forelse($material->checkouts->take(5) as $c)
                <div class="flex justify-between py-2 border-b border-gray-50 text-sm last:border-0">
                    <span>{{ $c->user->name }} × {{ $c->quantity }}</span>
                    <span class="text-gray-400">{{ $c->created_at->format('d.m.Y') }}</span>
                </div>
                @empty
                <p class="text-gray-400 text-sm">Keine Entnahmen.</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-sm space-y-2">
                <div class="flex justify-between"><span class="text-gray-500">Einheit</span><span>{{ $material->unit }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Mindestbestand</span><span>{{ $material->min_stock }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Prüfpflichtig</span><span>{{ $material->requires_inspection ? 'Ja' : 'Nein' }}</span></div>
                @if($material->requires_inspection && $material->inspection_interval_days)
                <div class="flex justify-between"><span class="text-gray-500">Intervall</span><span>{{ $material->inspection_interval_days }} Tage</span></div>
                @endif
                <div class="flex justify-between"><span class="text-gray-500">Verfallsdatum</span><span>{{ $material->has_expiry ? 'Ja' : 'Nein' }}</span></div>
            </div>

            @if($material->description)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Beschreibung</h3>
                <p class="text-sm text-gray-600">{{ $material->description }}</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
