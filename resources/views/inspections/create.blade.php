<x-app-layout>
    @section('title', 'Prüfung erfassen')
    <div class="max-w-lg">
        <div class="mb-6">
            <a href="{{ route('inspections.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Zurück</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Prüfung erfassen</h1>
        </div>
        <form method="POST" action="{{ route('inspections.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            @csrf
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-800">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Material *</label>
                <select name="material_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                    <option value="">Bitte wählen</option>
                    @foreach($materials as $m)
                    <option value="{{ $m->id }}" {{ old('material_id') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Standort *</label>
                <select name="location_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                    <option value="">Bitte wählen</option>
                    @foreach($locations as $l)
                    <option value="{{ $l->id }}" {{ old('location_id') == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ergebnis *</label>
                <select name="result" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                    <option value="ok" {{ old('result') == 'ok' ? 'selected' : '' }}>OK – einwandfrei</option>
                    <option value="defect" {{ old('result') == 'defect' ? 'selected' : '' }}>Defekt</option>
                    <option value="replaced" {{ old('result') == 'replaced' ? 'selected' : '' }}>Ersetzt</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nächste Prüfung</label>
                <input type="date" name="next_inspection_due" value="{{ old('next_inspection_due') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notiz</label>
                <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('notes') }}</textarea>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">Prüfung speichern</button>
        </form>
    </div>
</x-app-layout>
