<x-qr-layout :title="'Entnahme: ' . $medication->name">

    <div class="mb-5">
        <h1 class="text-2xl font-bold text-gray-900">Entnahme erfassen</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $medication->name }}</p>
    </div>

    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($batches->isEmpty())
        <div class="bg-white rounded-xl border border-gray-100 p-8 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-red-100 rounded-full mb-3">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 110 18A9 9 0 0112 3z"/>
                </svg>
            </div>
            <p class="text-gray-700 font-medium">Kein Bestand vorhanden</p>
            <p class="text-sm text-gray-400 mt-1">Für dieses Medikament ist aktuell kein Bestand eingebucht.</p>
        </div>
    @else
        <form method="POST" action="{{ route('med-qr.store', $medication) }}"
              class="bg-white rounded-xl border border-gray-100 p-6 space-y-5 max-w-lg mx-auto">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Charge <span class="text-red-500">*</span></label>
                <select name="inventory_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    @foreach($batches as $batch)
                    @php
                        $expired = $batch->expiry_date && $batch->expiry_date->isPast();
                        $soon    = $batch->expiry_date && !$expired && $batch->expiry_date->diffInDays(now()) <= 30;
                    @endphp
                    <option value="{{ $batch->id }}" {{ $expired ? 'disabled' : '' }}>
                        {{ $batch->batch_number ?? 'Keine Chargennr.' }}
                        – {{ $batch->quantity }} {{ $medication->unit }}
                        @if($batch->expiry_date)
                            (Verfall: {{ $batch->expiry_date->format('d.m.Y') }}{{ $expired ? ' – ABGELAUFEN' : ($soon ? ' – bald ablaufend' : '') }})
                        @endif
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Menge <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-2">
                    <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" required
                           class="w-32 border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <span class="text-sm text-gray-500">{{ $medication->unit }}</span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Entnommen von <span class="text-red-500">*</span></label>
                <input type="text" name="taken_by" value="{{ old('taken_by') }}" required placeholder="Vor- und Nachname"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Grund / Einsatz</label>
                <input type="text" name="reason" value="{{ old('reason') }}" placeholder="optional"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
            </div>

            <button type="submit"
                    class="w-full px-4 py-3 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">
                Entnahme bestätigen
            </button>
        </form>
    @endif

</x-qr-layout>
