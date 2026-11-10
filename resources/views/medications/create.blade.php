<x-app-layout>
    @section('title', 'Neues Medikament')

    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('medications.index') }}" class="hover:text-cd-red">Medikamente</a>
        <span>/</span>
        <span class="text-gray-800 font-medium">Neu</span>
    </div>

    <div class="max-w-lg">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h1 class="text-lg font-bold text-gray-900 mb-5">Neues Medikament anlegen</h1>

            <form method="POST" action="{{ route('medications.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cd-red/30">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Artikelnummer</label>
                    <input type="text" name="article_number" value="{{ old('article_number') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cd-red/30">
                    @error('article_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Einheit <span class="text-red-500">*</span></label>
                    <input type="text" name="unit" value="{{ old('unit', 'Stück') }}" required placeholder="z.B. Amp., Tbl., ml"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cd-red/30">
                    @error('unit')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mindestbestand</label>
                    <input type="number" name="min_stock" value="{{ old('min_stock', 0) }}" min="0"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cd-red/30">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
                    <textarea name="description" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cd-red/30 resize-none">{{ old('description') }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="px-5 py-2 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">Anlegen</button>
                    <a href="{{ route('medications.index') }}" class="px-5 py-2 border border-gray-200 text-gray-600 rounded-lg text-sm hover:border-gray-300">Abbrechen</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
