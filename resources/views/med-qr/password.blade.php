<x-qr-layout :title="'Medikamenten-Entnahme: ' . $medication->name">

    <div class="mb-6 text-center">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-100 rounded-full mb-3">
            <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $medication->name }}</h1>
        <p class="text-sm text-gray-500 mt-1">Medikamenten-Entnahme</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 p-6 max-w-sm mx-auto">
        @if($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('med-qr.auth', $medication) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Passwort</label>
                <input type="password" name="password" required autofocus
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
            </div>
            <button type="submit"
                    class="w-full px-4 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">
                Weiter zur Entnahme
            </button>
        </form>
    </div>

</x-qr-layout>
