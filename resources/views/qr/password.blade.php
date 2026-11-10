<x-qr-layout :title="'Prüfung ' . $equipment->name">

    <div class="min-h-[70vh] flex items-center justify-center">
        <div class="w-full max-w-sm">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                <div class="bg-cd-navy px-6 py-5 text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white/10 mb-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h1 class="text-white font-bold text-lg">Außenprüfung</h1>
                    <p class="text-cd-yellow font-semibold text-base mt-0.5">{{ $equipment->name }}</p>
                </div>

                <form method="POST" action="{{ route('qr.auth', $equipment) }}" class="p-6 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Prüfer-Passwort</label>
                        <input
                            type="password"
                            name="password"
                            autofocus
                            autocomplete="current-password"
                            class="w-full border {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-cd-red/30"
                            placeholder="••••••••">
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="w-full bg-cd-red hover:bg-[#b5010f] text-white font-semibold py-3 rounded-xl text-sm transition-colors">
                        Prüfprotokoll öffnen
                    </button>
                </form>

            </div>
            <p class="text-center text-xs text-gray-400 mt-4">
                Dieser Zugang ist ausschließlich für die Außenprüfung bestimmt.
            </p>
        </div>
    </div>

</x-qr-layout>
