<x-app-layout>
    @section('title', 'Medikamente')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Medikamente</h1>
        <a href="{{ route('medications.create') }}" class="px-4 py-2 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">+ Neu</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-4 p-4 space-y-3">
        <form method="GET" class="flex gap-3 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name / Artikelnummer..."
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 min-w-40">
            <input type="hidden" name="filter" value="{{ request('filter') }}">
            <button class="px-4 py-2 bg-gray-700 text-white rounded-lg text-sm">Suchen</button>
            @if(request('search') || request('filter'))
            <a href="{{ route('medications.index') }}" class="px-4 py-2 border border-gray-200 text-gray-500 rounded-lg text-sm hover:text-gray-700">Zurücksetzen</a>
            @endif
        </form>
        <div class="flex gap-2 flex-wrap">
            @php $activeFilter = request('filter'); @endphp
            <a href="{{ route('medications.index', array_merge(request()->except('filter','page'), [])) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors {{ !$activeFilter ? 'bg-gray-800 text-white border-gray-800' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                Alle
            </a>
            <a href="{{ route('medications.index', array_merge(request()->except('filter','page'), ['filter' => 'low_stock'])) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors {{ $activeFilter === 'low_stock' ? 'bg-orange-600 text-white border-orange-600' : 'border-gray-200 text-gray-600 hover:border-orange-300 hover:text-orange-700' }}">
                Unter Mindestbestand
            </a>
            <a href="{{ route('medications.index', array_merge(request()->except('filter','page'), ['filter' => 'expiring'])) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors {{ $activeFilter === 'expiring' ? 'bg-yellow-600 text-white border-yellow-600' : 'border-gray-200 text-gray-600 hover:border-yellow-300 hover:text-yellow-700' }}">
                Bald ablaufend
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Desktop --}}
        <table class="hidden md:table w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Medikament</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Gesamtbestand</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Nächster Verfall</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Chargen</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">QR</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($medications as $med)
                @php
                    $stock = $med->inventory->sum('quantity');
                    $low   = $med->min_stock > 0 && $stock < $med->min_stock;
                    $batches = $med->inventory->where('quantity', '>', 0)->sortBy('expiry_date');
                    $nextExpiry = $batches->first()?->expiry_date;
                    $expiredBatches = $med->inventory->where('quantity', '>', 0)->filter(fn($i) => $i->expiry_date && $i->expiry_date->isPast());
                    $expiringSoon   = $med->inventory->where('quantity', '>', 0)->filter(fn($i) => $i->expiry_date && !$i->expiry_date->isPast() && $i->expiry_date->diffInDays(now()) <= 30);
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <a href="{{ route('medications.show', $med) }}" class="font-medium text-gray-900 hover:text-cd-red">{{ $med->name }}</a>
                        @if($med->article_number)
                        <div class="text-xs text-gray-400">{{ $med->article_number }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="{{ $low ? 'text-red-600 font-semibold' : 'text-gray-900' }}">{{ $stock }} {{ $med->unit }}</span>
                        @if($low)<span class="ml-1 text-xs text-red-500">(Min. {{ $med->min_stock }})</span>@endif
                    </td>
                    <td class="px-4 py-3">
                        @if($nextExpiry)
                            @if($expiredBatches->count())
                                <span class="text-red-600 font-semibold text-xs">Abgelaufen</span>
                            @elseif($expiringSoon->count())
                                <span class="text-yellow-600 font-semibold text-xs">{{ $nextExpiry->format('d.m.Y') }}</span>
                            @else
                                <span class="text-gray-600 text-xs">{{ $nextExpiry->format('d.m.Y') }}</span>
                            @endif
                        @else
                            <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $med->inventory->where('quantity', '>', 0)->count() }} aktiv</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('med-qr.password', $med) }}" target="_blank"
                           class="text-xs text-blue-600 hover:text-blue-800">QR-Link</a>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('medications.edit', $med) }}" class="text-gray-400 hover:text-cd-red text-xs">Bearbeiten</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Keine Medikamente gefunden.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- Mobile --}}
        <div class="md:hidden divide-y divide-gray-100">
            @forelse($medications as $med)
            @php
                $stock = $med->inventory->sum('quantity');
                $low   = $med->min_stock > 0 && $stock < $med->min_stock;
                $batches = $med->inventory->where('quantity', '>', 0)->sortBy('expiry_date');
                $nextExpiry = $batches->first()?->expiry_date;
                $hasExpiredBatch = $med->inventory->where('quantity', '>', 0)->contains(fn($i) => $i->expiry_date && $i->expiry_date->isPast());
                $hasSoonBatch    = $med->inventory->where('quantity', '>', 0)->contains(fn($i) => $i->expiry_date && !$i->expiry_date->isPast() && $i->expiry_date->diffInDays(now()) <= 30);
            @endphp
            <div class="p-4">
                <div class="flex justify-between items-start">
                    <a href="{{ route('medications.show', $med) }}" class="font-semibold text-gray-900 hover:text-cd-red">{{ $med->name }}</a>
                    <span class="text-sm font-bold {{ $low ? 'text-red-600' : 'text-gray-800' }}">{{ $stock }} {{ $med->unit }}</span>
                </div>
                <div class="flex flex-wrap gap-3 mt-2 text-xs text-gray-500">
                    @if($nextExpiry)
                        <span class="{{ $hasExpiredBatch ? 'text-red-600 font-medium' : ($hasSoonBatch ? 'text-yellow-600 font-medium' : '') }}">
                            Verfall: {{ $nextExpiry->format('d.m.Y') }}
                        </span>
                    @endif
                    <a href="{{ route('med-qr.password', $med) }}" class="text-blue-600">QR-Entnahme</a>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-400 text-sm">Keine Medikamente gefunden.</div>
            @endforelse
        </div>

        <div class="px-4 py-3 border-t border-gray-50">{{ $medications->links() }}</div>
    </div>
</x-app-layout>
