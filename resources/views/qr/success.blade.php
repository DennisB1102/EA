<x-qr-layout title="Prüfung abgeschlossen">

    <div class="min-h-[70vh] flex items-center justify-center">
        <div class="w-full max-w-sm text-center">

            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-100 mb-5">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-1">Prüfung gespeichert</h1>
            <p class="text-gray-500 text-sm mb-1">
                <span class="font-semibold text-gray-700">{{ $equipment->name }}</span>
            </p>
            @if($date)
                <p class="text-gray-400 text-xs mb-6">{{ $date }}</p>
            @endif

            <div class="bg-green-50 border border-green-200 rounded-xl px-5 py-4 text-sm text-green-800 mb-6">
                Das Prüfprotokoll wurde erfolgreich gespeichert und ist im System hinterlegt.
            </div>

            <p class="text-xs text-gray-400">
                Diese Seite kann jetzt geschlossen werden.
            </p>

        </div>
    </div>

</x-qr-layout>
