<x-app-layout>
    @section('title', 'Dashboard')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-500 text-sm mt-1">Übersicht Einsatzmaterial</p>
    </div>

    {{-- KPI-Kacheln --}}
    <div class="grid grid-cols-3 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-3.5 sm:p-5 border border-gray-100">
            <div class="text-xs sm:text-sm text-gray-500 leading-tight">Materialien</div>
            <div class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ $totalMaterials }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-3.5 sm:p-5 border border-gray-100">
            <div class="text-xs sm:text-sm text-gray-500 leading-tight">
                <span class="sm:hidden">Entnahmen</span>
                <span class="hidden sm:inline">Offene Entnahmen</span>
            </div>
            <div class="text-2xl sm:text-3xl font-bold {{ $openCheckouts > 0 ? 'text-amber-600' : 'text-gray-900' }} mt-1">{{ $openCheckouts }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-3.5 sm:p-5 border border-gray-100">
            <div class="text-xs sm:text-sm text-gray-500 leading-tight">
                <span class="sm:hidden">Mindestbestand</span>
                <span class="hidden sm:inline">Unter Mindestbestand</span>
            </div>
            <div class="text-2xl sm:text-3xl font-bold {{ $lowStockMaterials->count() > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">{{ $lowStockMaterials->count() }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-3.5 sm:p-5 border border-gray-100">
            <div class="text-xs sm:text-sm text-gray-500 leading-tight">
                <span class="sm:hidden">Ablaufend</span>
                <span class="hidden sm:inline">Bald abgelaufen</span>
            </div>
            <div class="text-2xl sm:text-3xl font-bold {{ $expiringSoon->count() > 0 ? 'text-orange-600' : 'text-gray-900' }} mt-1">{{ $expiringSoon->count() }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-3.5 sm:p-5 border border-gray-100">
            <div class="text-xs sm:text-sm text-gray-500 leading-tight">
                <span class="sm:hidden">Geräte</span>
                <span class="hidden sm:inline">Geräte-Warnungen</span>
            </div>
            <div class="text-2xl sm:text-3xl font-bold {{ $deviceWarnings->count() > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">{{ $deviceWarnings->count() }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-3.5 sm:p-5 border border-gray-100">
            <div class="text-xs sm:text-sm text-gray-500 leading-tight">
                <span class="sm:hidden">Mängel</span>
                <span class="hidden sm:inline">Offene Mängel</span>
            </div>
            <div class="text-2xl sm:text-3xl font-bold {{ $openDefects->count() > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">{{ $openDefects->count() }}</div>
        </div>
    </div>

    {{-- Offene Mängel --}}
    @if($openDefects->count() > 0)
    <div class="mb-6 bg-white rounded-xl shadow-sm border border-red-100 p-5">
        <h2 class="font-semibold text-red-700 mb-3">Offene Mängel ({{ $openDefects->count() }})</h2>
        <div class="space-y-2">
            @foreach($openDefects as $defect)
            @php
                $label  = $defect->material ? $defect->material->name : ($defect->deviceInstance?->material->name ?? '—');
                $equip  = $defect->protocol->equipment;
                $veh    = $equip->vehicle;
                $url    = route('vehicles.equipment.protocols.show', [$veh, $equip, $defect->protocol]);
            @endphp
            <a href="{{ $url }}" class="flex items-start gap-3 p-2.5 bg-red-50 rounded-lg text-sm hover:bg-red-100 transition-colors">
                <span class="w-2 h-2 rounded-full bg-red-500 shrink-0 mt-1.5"></span>
                <div class="flex-1 min-w-0">
                    <span class="font-medium text-red-900">{{ $label }}</span>
                    <span class="text-red-600 ml-2 text-xs">{{ $equip->name }} · {{ $defect->protocol->checked_at->format('d.m.Y') }}</span>
                    @if($defect->notes)
                        <div class="text-xs text-red-700 truncate italic mt-0.5">{{ $defect->notes }}</div>
                    @endif
                </div>
                <span class="text-xs text-red-400 shrink-0 mt-0.5">→</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @if($lowStockMaterials->count() || $expiredItems->count() || $expiringSoon->count() || $deviceWarnings->count())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="font-semibold text-gray-800 mb-3">Warnungen</h2>
            <div class="space-y-2">
                @foreach($expiredItems as $item)
                <div class="flex items-start gap-2.5 p-2.5 bg-red-50 rounded-lg text-sm">
                    <span class="w-2 h-2 rounded-full bg-red-500 shrink-0 mt-1.5"></span>
                    <div class="min-w-0">
                        <span class="font-semibold text-red-700 text-xs uppercase tracking-wide">Abgelaufen</span>
                        <div class="text-red-800 truncate">{{ $item->material->name }}</div>
                        <div class="text-red-600 text-xs">{{ $item->location->name }} · {{ $item->expiry_date->format('d.m.Y') }}</div>
                    </div>
                </div>
                @endforeach
                @foreach($lowStockMaterials as $m)
                <div class="flex items-start gap-2.5 p-2.5 bg-orange-50 rounded-lg text-sm">
                    <span class="w-2 h-2 rounded-full bg-orange-500 shrink-0 mt-1.5"></span>
                    <div class="min-w-0">
                        <span class="font-semibold text-orange-700 text-xs uppercase tracking-wide">Niedrig</span>
                        <div class="text-orange-800 truncate">{{ $m->name }}</div>
                        <div class="text-orange-600 text-xs">{{ $m->totalStock() }} / {{ $m->min_stock }} Mindestbestand</div>
                    </div>
                </div>
                @endforeach
                @foreach($expiringSoon as $item)
                <div class="flex items-start gap-2.5 p-2.5 bg-yellow-50 rounded-lg text-sm">
                    <span class="w-2 h-2 rounded-full bg-yellow-500 shrink-0 mt-1.5"></span>
                    <div class="min-w-0">
                        <span class="font-semibold text-yellow-700 text-xs uppercase tracking-wide">Läuft ab</span>
                        <div class="text-yellow-800 truncate">{{ $item->material->name }}</div>
                        <div class="text-yellow-600 text-xs">{{ $item->location->name }} · {{ $item->expiry_date->format('d.m.Y') }}</div>
                    </div>
                </div>
                @endforeach
                @foreach($deviceWarnings as $inst)
                @php
                    $mtkExpired  = $inst->mtk_ablauf && $inst->mtk_ablauf->isPast();
                    $stkExpired  = $inst->stk_ablauf && $inst->stk_ablauf->isPast();
                    $isExpired   = $mtkExpired || $stkExpired;
                    $bgClass     = $isExpired ? 'bg-red-50' : 'bg-yellow-50';
                    $dotClass    = $isExpired ? 'bg-red-500' : 'bg-yellow-500';
                    $textClass   = $isExpired ? 'text-red-800' : 'text-yellow-800';
                @endphp
                <a href="{{ route('materials.show', $inst->material) }}"
                   class="flex items-start gap-2.5 p-2.5 {{ $bgClass }} rounded-lg text-sm hover:opacity-80">
                    <span class="w-2 h-2 rounded-full {{ $dotClass }} shrink-0 mt-1.5"></span>
                    <div class="min-w-0 flex-1">
                        <span class="font-semibold {{ $textClass }} text-xs uppercase tracking-wide">
                            {{ $isExpired ? 'Prüfung abgelaufen' : 'Prüfung fällig' }}
                        </span>
                        <div class="{{ $textClass }} truncate">{{ $inst->material->name }}</div>
                        <div class="text-xs {{ $textClass }} opacity-80 flex flex-wrap gap-x-2">
                            @if($inst->serial_number)<span>SN {{ $inst->serial_number }}</span>@endif
                            @if($inst->mtk_ablauf && $inst->mtk_ablauf <= now()->addDays(30))
                                <span>MTK {{ $inst->mtk_ablauf->format('d.m.Y') }}</span>
                            @endif
                            @if($inst->stk_ablauf && $inst->stk_ablauf <= now()->addDays(30))
                                <span>STK {{ $inst->stk_ablauf->format('d.m.Y') }}</span>
                            @endif
                        </div>
                    </div>
                    <span class="text-xs {{ $textClass }} opacity-50 shrink-0">›</span>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex justify-between items-center mb-3">
                <h2 class="font-semibold text-gray-800">Letzte Entnahmen</h2>
                <a href="{{ route('checkouts.index') }}" class="text-sm text-cd-red hover:underline">Alle anzeigen</a>
            </div>
            @forelse($recentCheckouts as $c)
            <div class="flex items-center justify-between py-2 border-b border-gray-50 text-sm last:border-0">
                <div>
                    <span class="font-medium">{{ $c->material->name }}</span>
                    <span class="text-gray-400 ml-1">× {{ $c->quantity }}</span>
                </div>
                <div class="text-right text-gray-500 text-xs">
                    {{ $c->user->name }}<br>
                    {{ $c->created_at->format('d.m. H:i') }}
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-sm">Keine Entnahmen vorhanden.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
