<x-app-layout>
    @section('title', 'Einstellungen')

    <div class="mb-5">
        <h1 class="text-2xl font-bold text-gray-900">Einstellungen</h1>
        <p class="text-sm text-gray-500 mt-0.5">Systemweite Konfiguration</p>
    </div>

    <div class="max-w-lg space-y-5">

        {{-- QR-Außenprüfung --}}
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">NFR Außenprüfung</h2>
            </div>
            <form method="POST" action="{{ route('admin.settings.update') }}" class="p-5 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Prüfer-Passwort (QR-Code)
                    </label>
                    <div class="flex gap-2">
                        <input
                            type="text"
                            name="nfr_qr_password"
                            value="{{ old('nfr_qr_password', $qrPassword) }}"
                            class="flex-1 border {{ $errors->has('nfr_qr_password') ? 'border-red-400 bg-red-50' : 'border-gray-200' }} rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cd-red/30 font-mono"
                            autocomplete="off">
                        <button type="button"
                                onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'text' ? 'password' : 'text'"
                                class="px-3 py-2 border border-gray-200 rounded-lg text-xs text-gray-500 hover:text-gray-700 shrink-0">
                            Anzeigen
                        </button>
                    </div>
                    @error('nfr_qr_password')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-gray-400">
                        Dieses Passwort wird beim Scannen eines NFR-QR-Codes abgefragt. Mindestens 4 Zeichen.
                    </p>
                </div>

                <div class="pt-1">
                    <button type="submit"
                            class="px-4 py-2 bg-cd-red text-white text-sm font-semibold rounded-lg hover:bg-[#b5010f]">
                        Speichern
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
