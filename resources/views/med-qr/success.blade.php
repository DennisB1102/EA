<x-qr-layout :title="'Entnahme erfasst'">

    <div class="text-center py-10">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Entnahme erfasst</h1>
        <p class="text-gray-500 mb-1">{{ $medication->name }}</p>
        <p class="text-sm text-gray-400">Die Entnahme wurde erfolgreich dokumentiert.</p>

        <div class="mt-8">
            <a href="{{ route('med-qr.password', $medication) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 text-gray-600 rounded-xl text-sm hover:border-gray-300">
                Weitere Entnahme
            </a>
        </div>
    </div>

</x-qr-layout>
