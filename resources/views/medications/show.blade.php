<x-app-layout>
    @section('title', $medication->name)

    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('medications.index') }}" class="hover:text-cd-red">Medikamente</a>
        <span>/</span>
        <span class="text-gray-800 font-medium">{{ $medication->name }}</span>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">

        {{-- ── Linke Spalte ── --}}
        <div class="flex-1 min-w-0 space-y-6">

            {{-- Infokarte --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $medication->name }}</h1>
                        @if($medication->article_number)
                        <p class="text-sm text-gray-400 mt-0.5">{{ $medication->article_number }}</p>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('medications.edit', $medication) }}"
                           class="px-3 py-1.5 border border-gray-200 text-gray-600 rounded-lg text-xs hover:border-gray-300">Bearbeiten</a>
                    </div>
                </div>
                @if($medication->description)
                <p class="text-sm text-gray-600 mb-4">{{ $medication->description }}</p>
                @endif
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
                    @php
                        $totalStock = $medication->inventory->sum('quantity');
                        $low = $medication->min_stock > 0 && $totalStock < $medication->min_stock;
                    @endphp
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Gesamtbestand</p>
                        <p class="font-semibold {{ $low ? 'text-red-600' : 'text-gray-900' }}">{{ $totalStock }} {{ $medication->unit }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Mindestbestand</p>
                        <p class="font-semibold text-gray-900">{{ $medication->min_stock }} {{ $medication->unit }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">QR-Entnahme</p>
                        <a href="{{ route('med-qr.password', $medication) }}" target="_blank"
                           class="text-blue-600 text-xs hover:underline font-medium">Link öffnen →</a>
                    </div>
                </div>
            </div>

            {{-- Chargen / Bestand --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h2 class="text-sm font-semibold text-gray-700">Chargen & Bestand</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($medication->inventory->sortBy('expiry_date') as $batch)
                    @php
                        $expired = $batch->expiry_date && $batch->expiry_date->isPast();
                        $soon    = $batch->expiry_date && !$expired && $batch->expiry_date->diffInDays(now()) <= 30;
                    @endphp
                    <div class="px-5 py-3 flex flex-wrap items-center gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                @if($expired)
                                    <span class="w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
                                @elseif($soon)
                                    <span class="w-2 h-2 rounded-full bg-yellow-400 shrink-0"></span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-green-500 shrink-0"></span>
                                @endif
                                <span class="font-medium text-sm text-gray-800">
                                    @if($batch->batch_number) {{ $batch->batch_number }} @else <span class="text-gray-400">Keine Chargennr.</span> @endif
                                </span>
                                @if($expired)
                                    <span class="text-xs bg-red-100 text-red-700 px-1.5 py-0.5 rounded">Abgelaufen</span>
                                @elseif($soon)
                                    <span class="text-xs bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded">Bald ablaufend</span>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-4 mt-1 text-xs text-gray-500">
                                @if($batch->expiry_date)
                                <span>Verfall: <strong class="{{ $expired ? 'text-red-600' : ($soon ? 'text-yellow-600' : 'text-gray-700') }}">{{ $batch->expiry_date->format('d.m.Y') }}</strong></span>
                                @endif
                                <span>Bestand: <strong class="text-gray-700">{{ $batch->quantity }} {{ $medication->unit }}</strong></span>
                                @if($batch->notes)<span>{{ $batch->notes }}</span>@endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            {{-- Edit Batch --}}
                            <button onclick="document.getElementById('edit-batch-{{ $batch->id }}').classList.toggle('hidden')"
                                    class="text-xs text-gray-400 hover:text-cd-red border border-gray-200 rounded px-2 py-1">Ändern</button>
                            {{-- Delete Batch --}}
                            <form method="POST" action="{{ route('medications.batches.destroy', [$medication, $batch]) }}"
                                  onsubmit="return confirm('Charge wirklich löschen?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-400 hover:text-red-600 border border-red-100 rounded px-2 py-1">Löschen</button>
                            </form>
                        </div>
                    </div>
                    {{-- Edit form (hidden) --}}
                    <div id="edit-batch-{{ $batch->id }}" class="hidden px-5 py-3 bg-gray-50 border-t border-gray-100">
                        <form method="POST" action="{{ route('medications.batches.update', [$medication, $batch]) }}" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @csrf @method('PATCH')
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Chargennr.</label>
                                <input type="text" name="batch_number" value="{{ $batch->batch_number }}"
                                       class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Verfall</label>
                                <input type="date" name="expiry_date" value="{{ $batch->expiry_date?->format('Y-m-d') }}" required
                                       class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Bestand</label>
                                <input type="number" name="quantity" value="{{ $batch->quantity }}" min="0" required
                                       class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Notiz</label>
                                <input type="text" name="notes" value="{{ $batch->notes }}"
                                       class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm">
                            </div>
                            <div class="col-span-2 sm:col-span-4 flex gap-2">
                                <button class="px-3 py-1.5 bg-cd-red text-white rounded text-xs font-medium">Speichern</button>
                                <button type="button" onclick="document.getElementById('edit-batch-{{ $batch->id }}').classList.add('hidden')"
                                        class="px-3 py-1.5 border border-gray-200 text-gray-600 rounded text-xs">Abbrechen</button>
                            </div>
                        </form>
                    </div>
                    @empty
                    <div class="px-5 py-6 text-center text-gray-400 text-sm">Noch keine Chargen angelegt.</div>
                    @endforelse
                </div>

                {{-- Neue Charge --}}
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Neue Charge hinzufügen</p>
                    <form method="POST" action="{{ route('medications.batches.store', $medication) }}"
                          class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @csrf
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Chargennr.</label>
                            <input type="text" name="batch_number" placeholder="optional"
                                   class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Verfall <span class="text-red-500">*</span></label>
                            <input type="date" name="expiry_date" required
                                   class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Menge <span class="text-red-500">*</span></label>
                            <input type="number" name="quantity" min="1" required placeholder="z.B. 10"
                                   class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Notiz</label>
                            <input type="text" name="notes" placeholder="optional"
                                   class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm">
                        </div>
                        <div class="col-span-2 sm:col-span-4">
                            <button class="px-4 py-1.5 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">
                                Charge hinzufügen
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Entnahme erfassen (intern) --}}
            @if($medication->inventory->where('quantity', '>', 0)->count())
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700">Entnahme erfassen</h2>
                </div>
                <div class="p-5">
                    <form method="POST" action="{{ route('medications.checkout.store', $medication) }}"
                          class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @csrf
                        <div class="col-span-2">
                            <label class="block text-xs text-gray-500 mb-1">Charge <span class="text-red-500">*</span></label>
                            <select name="inventory_id" required class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm">
                                @foreach($medication->inventory->where('quantity', '>', 0)->sortBy('expiry_date') as $batch)
                                <option value="{{ $batch->id }}">
                                    {{ $batch->batch_number ?? 'Keine Chargennr.' }}
                                    – {{ $batch->quantity }} {{ $medication->unit }}
                                    @if($batch->expiry_date) (Verfall: {{ $batch->expiry_date->format('d.m.Y') }}) @endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Menge <span class="text-red-500">*</span></label>
                            <input type="number" name="quantity" min="1" required placeholder="z.B. 1"
                                   class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Entnommen von <span class="text-red-500">*</span></label>
                            <input type="text" name="taken_by" required placeholder="Name"
                                   class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs text-gray-500 mb-1">Grund</label>
                            <input type="text" name="reason" placeholder="optional"
                                   class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm">
                        </div>
                        <div class="col-span-2 sm:col-span-4">
                            <button class="px-4 py-1.5 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-gray-700">
                                Entnahme speichern
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

        </div>

        {{-- ── Rechte Spalte: Entnahmen-Historie ── --}}
        <div class="w-full lg:w-72 shrink-0 space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-sm font-semibold text-gray-700">Entnahmen</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($checkouts as $co)
                    <div class="px-4 py-3 text-sm">
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-800">{{ $co->taken_by }}</span>
                            <span class="text-gray-500">{{ $co->quantity }} {{ $medication->unit }}</span>
                        </div>
                        <div class="text-xs text-gray-400 mt-0.5 flex items-center gap-2">
                            <span>{{ $co->created_at->format('d.m.Y H:i') }}</span>
                            @if($co->via_qr)<span class="bg-blue-100 text-blue-600 px-1 rounded">QR</span>@endif
                        </div>
                        @if($co->reason)<div class="text-xs text-gray-500 mt-0.5">{{ $co->reason }}</div>@endif
                        @if($co->inventory)
                        <div class="text-xs text-gray-400">Charge: {{ $co->inventory->batch_number ?? '—' }}</div>
                        @endif
                    </div>
                    @empty
                    <div class="px-4 py-6 text-center text-gray-400 text-xs">Noch keine Entnahmen.</div>
                    @endforelse
                </div>
                @if($checkouts->hasPages())
                <div class="px-4 py-2 border-t border-gray-50 text-xs">{{ $checkouts->links() }}</div>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>
