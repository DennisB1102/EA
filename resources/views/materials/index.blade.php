<x-app-layout>
    @section('title', 'Materialien')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Materialien</h1>
        <a href="{{ route('materials.create') }}" class="px-4 py-2 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">+ Neu</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-4 p-4 space-y-3">
        <form method="GET" class="flex gap-3 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name / Artikelnummer..."
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 min-w-40">
            <select name="category" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Alle Kategorien</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <input type="hidden" name="filter" value="{{ request('filter') }}">
            <button class="px-4 py-2 bg-gray-700 text-white rounded-lg text-sm">Suchen</button>
            @if(request('search') || request('category') || request('filter'))
            <a href="{{ route('materials.index') }}" class="px-4 py-2 border border-gray-200 text-gray-500 rounded-lg text-sm hover:text-gray-700">Zurücksetzen</a>
            @endif
        </form>
        <div class="flex gap-2 flex-wrap">
            @php $activeFilter = request('filter'); @endphp
            <a href="{{ route('materials.index', array_merge(request()->except('filter','page'), [])) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors
                      {{ !$activeFilter ? 'bg-gray-800 text-white border-gray-800' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                Alle
            </a>
            <a href="{{ route('materials.index', array_merge(request()->except('filter','page'), ['filter' => 'low_stock'])) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors
                      {{ $activeFilter === 'low_stock' ? 'bg-orange-600 text-white border-orange-600' : 'border-gray-200 text-gray-600 hover:border-orange-300 hover:text-orange-700' }}">
                Unter Mindestbestand
            </a>
            <a href="{{ route('materials.index', array_merge(request()->except('filter','page'), ['filter' => 'devices'])) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors
                      {{ $activeFilter === 'devices' ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-700' }}">
                Nur Geräte
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Desktop Tabelle --}}
        <table class="hidden md:table w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Material</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Kategorie</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Einheit</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Gesamtbestand</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Mindest</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($materials as $m)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('materials.show', $m) }}" class="font-medium text-gray-900 hover:text-cd-red">{{ $m->name }}</a>
                            @if($m->is_device)
                            <span class="text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-medium">Gerät</span>
                            @endif
                        </div>
                        @if($m->article_number)
                        <div class="text-xs text-gray-400">{{ $m->article_number }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full" style="background:{{ $m->category->color }}"></span>
                            {{ $m->category->name }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $m->unit }}</td>
                    <td class="px-4 py-3">
                        @php $stock = $m->totalStock(); @endphp
                        <span class="{{ $m->isBelowMinStock() && $m->min_stock > 0 ? 'text-red-600 font-semibold' : 'text-gray-900' }}">{{ $stock }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $m->min_stock }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('materials.edit', $m) }}" class="text-gray-400 hover:text-cd-red text-xs">Bearbeiten</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Keine Materialien gefunden.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-gray-100">
            @forelse($materials as $m)
            @php $stock = $m->totalStock(); $low = $m->isBelowMinStock() && $m->min_stock > 0; @endphp
            <div class="p-4">
                <div class="flex justify-between items-start gap-2">
                    <div class="flex items-center gap-1.5 flex-wrap">
                    <a href="{{ route('materials.show', $m) }}" class="font-semibold text-gray-900 hover:text-cd-red leading-snug">{{ $m->name }}</a>
                    @if($m->is_device)
                    <span class="text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-medium">Gerät</span>
                    @endif
                </div>
                    <span class="text-sm font-bold shrink-0 {{ $low ? 'text-red-600' : 'text-gray-800' }}">{{ $stock }} {{ $m->unit }}</span>
                </div>
                @if($m->article_number)
                <div class="text-xs text-gray-400 mt-0.5">{{ $m->article_number }}</div>
                @endif
                <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-gray-500">
                    <span class="inline-flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full" style="background:{{ $m->category->color }}"></span>
                        {{ $m->category->name }}
                    </span>
                    @if($m->min_stock > 0)
                    <span>Mindest: {{ $m->min_stock }}</span>
                    @endif
                </div>
                <div class="mt-2 pt-2 border-t border-gray-50">
                    <a href="{{ route('materials.edit', $m) }}" class="text-xs text-gray-400 hover:text-cd-red">Bearbeiten</a>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-400 text-sm">Keine Materialien gefunden.</div>
            @endforelse
        </div>

        <div class="px-4 py-3 border-t border-gray-50">{{ $materials->links() }}</div>
    </div>
</x-app-layout>
