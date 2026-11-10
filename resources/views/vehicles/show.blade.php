@use('Illuminate\Support\Facades\Storage')
<x-app-layout>
    @section('title', $vehicle->name)

    {{-- ── Kopfzeile ─────────────────────────────────────────────────────── --}}
    <div class="mb-5 flex justify-between items-start">
        <div>
            <a href="{{ route('vehicles.index') }}" class="text-sm text-gray-500">← Fahrzeuge</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $vehicle->name }}</h1>
            @if($vehicle->call_sign)
                <p class="text-gray-500 font-mono text-sm mt-0.5">{{ $vehicle->call_sign }}</p>
            @endif
            <p class="text-sm text-gray-400 mt-0.5">{{ $vehicle->location->name }}</p>
        </div>
        <a href="{{ route('vehicles.edit', $vehicle) }}"
           class="shrink-0 px-3 py-1.5 border border-gray-200 rounded-lg text-sm text-gray-600 hover:text-cd-red">
            Bearbeiten
        </a>
    </div>

    {{-- ── Fahrzeugbild ────────────────────────────────────────────────────── --}}
    @if($vehicle->image_path)
    <div class="mb-5 bg-white rounded-xl border border-gray-100 p-3 flex justify-center">
        <img src="{{ asset('storage/' . $vehicle->image_path) }}"
             alt="{{ $vehicle->name }}" class="max-h-28 object-contain">
    </div>
    @endif

    {{-- ── Fahrzeug-Details (horizontal scroll auf Mobile) ──────────────────── --}}
    <div class="flex gap-3 overflow-x-auto pb-1 mb-6 -mx-4 px-4 sm:mx-0 sm:px-0 sm:grid sm:grid-cols-4">
        <div class="bg-white rounded-xl border border-gray-100 p-3.5 shrink-0 min-w-[120px] sm:min-w-0">
            <p class="text-xs text-gray-400 mb-1">Kennzeichen</p>
            <p class="font-semibold text-gray-900 text-sm">{{ $vehicle->license_plate ?: '—' }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-3.5 shrink-0 min-w-[120px] sm:min-w-0">
            <p class="text-xs text-gray-400 mb-1">TÜV fällig</p>
            @if($vehicle->tuev_date)
                <p class="font-semibold text-sm {{ $vehicle->isTuevExpired() ? 'text-red-600' : ($vehicle->isTuevExpiringSoon() ? 'text-yellow-600' : 'text-gray-900') }}">
                    {{ $vehicle->tuev_date->format('d.m.Y') }}
                </p>
            @else
                <p class="font-semibold text-gray-400 text-sm">—</p>
            @endif
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-3.5 shrink-0 min-w-[120px] sm:min-w-0">
            <p class="text-xs text-gray-400 mb-1">Status</p>
            <p class="font-semibold text-gray-900 text-sm">{{ $vehicle->is_active ? 'Aktiv' : 'Inaktiv' }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-3.5 shrink-0 min-w-[120px] sm:min-w-0">
            <p class="text-xs text-gray-400 mb-1">Ausstattung</p>
            <p class="font-semibold text-gray-900 text-sm">{{ $vehicle->equipment->count() }}</p>
        </div>
    </div>

    @php
        $nfrs  = $vehicle->equipment->where('type', 'notfallrucksack')->sortBy('name');
        $other = $vehicle->equipment->where('type', '!=', 'notfallrucksack')->sortBy('name');
    @endphp

    {{-- ── NFR-Kacheln ────────────────────────────────────────────────────── --}}
    @if($nfrs->isNotEmpty())
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-base font-semibold text-gray-900">Notfallrucksäcke</h2>
        <a href="{{ route('vehicles.equipment.create', $vehicle) }}"
           class="px-3 py-1.5 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">
            + Ausstattung
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5 mb-8">
        @foreach($nfrs as $nfr)
        @php
            $lastProtocol   = $nfr->protocols->sortByDesc('checked_at')->first();
            $daysSince      = $lastProtocol ? $lastProtocol->checked_at->diffInDays(now()) : null;
            $openDefects    = $nfr->protocols
                ->flatMap(fn($p) => $p->items)
                ->filter(fn($i) => !$i->is_ok && $i->resolved_at === null)
                ->count();
            $deviceWarnings = $nfr->assignedDevices->filter(function($d) {
                return ($d->mtk_ablauf && $d->mtk_ablauf->lte(now()->addDays(30)))
                    || ($d->stk_ablauf && $d->stk_ablauf->lte(now()->addDays(30)));
            })->count();

            $hasProblems = $openDefects > 0;
            $isOverdue   = $daysSince === null || $daysSince > 30;
            $hasWarnings = $deviceWarnings > 0;

            [$borderClass, $dotClass] = match(true) {
                $hasProblems => ['border-red-300 bg-red-50/50',    'bg-red-500'],
                $isOverdue   => ['border-orange-200 bg-orange-50/30', 'bg-orange-400'],
                default      => ['border-green-200 bg-green-50/20',   'bg-green-500'],
            };
        @endphp

        <a href="{{ route('vehicles.equipment.show', [$vehicle, $nfr]) }}"
           class="group relative flex flex-col bg-white rounded-2xl border-2 {{ $borderClass }} p-3 sm:p-4 hover:shadow-md transition-all duration-150 min-h-[96px]">

            {{-- Status-Punkt --}}
            <span class="absolute top-2.5 right-2.5 w-2.5 h-2.5 rounded-full {{ $dotClass }}"></span>

            {{-- Name --}}
            <div class="font-bold text-gray-900 text-sm leading-tight mb-2 pr-4">
                {{ $nfr->name }}
            </div>

            {{-- Letzte Prüfung --}}
            <div class="text-xs leading-snug mb-2">
                @if($lastProtocol)
                    <span class="{{ $daysSince > 30 ? 'text-orange-600 font-medium' : 'text-gray-400' }}">
                        {{-- Datum kürzen auf Mobile --}}
                        <span class="sm:hidden">{{ $lastProtocol->checked_at->format('d.m.y') }}</span>
                        <span class="hidden sm:inline">{{ $lastProtocol->checked_at->format('d.m.Y') }}</span>
                    </span>
                @else
                    <span class="text-gray-400 italic">Nie geprüft</span>
                @endif
            </div>

            {{-- Badges / Status --}}
            <div class="flex flex-wrap gap-1 mt-auto">
                @if($openDefects > 0)
                    <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full bg-red-100 text-red-700">
                        ✗ {{ $openDefects }}
                    </span>
                @endif
                @if($hasWarnings)
                    <span class="text-xs font-semibold px-1.5 py-0.5 rounded-full bg-yellow-100 text-yellow-700">
                        ⚠ {{ $deviceWarnings }}
                    </span>
                @endif
                @if(!$hasProblems && !$hasWarnings && !$isOverdue)
                    <span class="text-xs text-green-600 font-medium">i.O.</span>
                @endif
            </div>
        </a>
        @endforeach
    </div>
    @endif

    {{-- ── Weitere Ausstattung ──────────────────────────────────────────────── --}}
    @if($other->isNotEmpty())
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-base font-semibold text-gray-900">Weitere Ausstattung</h2>
        @if($nfrs->isEmpty())
        <a href="{{ route('vehicles.equipment.create', $vehicle) }}"
           class="px-3 py-1.5 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">
            + Ausstattung
        </a>
        @endif
    </div>

    <div class="space-y-2">
        @foreach($other as $equip)
        {{-- Mobile: gesamte Karte klickbar; Desktop: Aktionslinks in der Zeile --}}
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">

            {{-- Hauptzeile: immer klickbar --}}
            <a href="{{ route('vehicles.equipment.show', [$vehicle, $equip]) }}"
               class="flex items-center justify-between px-4 py-3 hover:bg-gray-50/60 transition-colors">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="shrink-0 text-xs px-2 py-0.5 rounded-full font-medium
                        @if($equip->type === 'medizingeraet') bg-blue-50 text-blue-700
                        @elseif($equip->type === 'trage') bg-purple-50 text-purple-700
                        @else bg-gray-100 text-gray-500
                        @endif">
                        {{ $equip->getTypeLabel() }}
                    </span>
                    <span class="font-medium text-gray-900 text-sm truncate">{{ $equip->name }}</span>
                    @if(!$equip->is_active)
                        <span class="shrink-0 text-xs text-gray-400">(Inaktiv)</span>
                    @endif
                </div>
                <div class="flex items-center gap-2 shrink-0 ml-2">
                    <span class="text-xs text-gray-400">{{ $equip->inventory->count() }} Pos.</span>
                    <span class="text-gray-300 text-sm">›</span>
                </div>
            </a>

            {{-- Aktionsleiste: nur auf sm+ sichtbar --}}
            <div class="hidden sm:flex items-center gap-4 px-4 py-2 border-t border-gray-50 bg-gray-50/40">
                <a href="{{ route('vehicles.equipment.edit', [$vehicle, $equip]) }}"
                   class="text-xs text-gray-500 hover:text-cd-red">Bearbeiten</a>
                <form method="POST" action="{{ route('vehicles.equipment.destroy', [$vehicle, $equip]) }}"
                      onsubmit="return confirm('Ausstattung „{{ $equip->name }}" wirklich löschen?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs text-gray-400 hover:text-red-600">Löschen</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @if($vehicle->equipment->isEmpty())
    <div class="text-center py-12">
        <p class="text-gray-400 mb-4">Noch keine Ausstattung zugeordnet.</p>
        <a href="{{ route('vehicles.equipment.create', $vehicle) }}"
           class="px-4 py-2 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">
            + Ausstattung hinzufügen
        </a>
    </div>
    @endif
</x-app-layout>
