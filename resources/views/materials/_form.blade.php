@if($errors->any())
<div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-800 mb-4">
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

{{-- Gerät-Toggle --}}
<div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-100 rounded-lg mb-4">
    <input type="checkbox" name="is_device" id="is_device" value="1"
        {{ old('is_device', $material->is_device ?? false) ? 'checked' : '' }}
        class="rounded border-gray-300 accent-blue-600 w-4 h-4 cursor-pointer">
    <label for="is_device" class="text-sm font-medium text-blue-800 cursor-pointer select-none">
        Klasse: <strong>Gerät</strong> — Seriennummer, MTK- und STK-Ablauf
    </label>
</div>

<div class="grid grid-cols-2 gap-4">

    {{-- Immer sichtbar --}}
    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
        <input type="text" name="name" value="{{ old('name', $material->name ?? '') }}"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Artikelnummer</label>
        <input type="text" name="article_number" value="{{ old('article_number', $material->article_number ?? '') }}"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kategorie *</label>
        <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            <option value="">Bitte wählen</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('category_id', $material->category_id ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
        <textarea name="description" rows="2"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('description', $material->description ?? '') }}</textarea>
    </div>

    {{-- Nur für Verbrauchsmaterial --}}
    <div id="consumable-fields" class="col-span-2 grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Einheit *</label>
            <input type="text" name="unit" value="{{ old('unit', $material->unit ?? 'Stück') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mindestbestand</label>
            <input type="number" name="min_stock" value="{{ old('min_stock', $material->min_stock ?? 0) }}" min="0"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="flex items-center gap-3">
            <input type="checkbox" name="requires_inspection" id="req_insp" value="1"
                {{ old('requires_inspection', $material->requires_inspection ?? false) ? 'checked' : '' }}
                class="rounded border-gray-300">
            <label for="req_insp" class="text-sm text-gray-700">Prüfpflichtig</label>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prüfintervall (Tage)</label>
            <input type="number" name="inspection_interval_days" min="1"
                value="{{ old('inspection_interval_days', $material->inspection_interval_days ?? '') }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="flex items-center gap-3 col-span-2">
            <input type="checkbox" name="has_expiry" id="has_expiry" value="1"
                {{ old('has_expiry', $material->has_expiry ?? false) ? 'checked' : '' }}
                class="rounded border-gray-300">
            <label for="has_expiry" class="text-sm text-gray-700">Hat Verfallsdatum</label>
        </div>
    </div>

    {{-- Nur für Geräte --}}
    <div id="device-fields" class="col-span-2 hidden">
        <div class="border border-blue-100 rounded-xl overflow-hidden">
            <div class="bg-blue-50 px-4 py-2.5 border-b border-blue-100">
                <p class="text-xs font-semibold text-blue-700 uppercase tracking-wider">Gerätedaten</p>
            </div>
            <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Seriennummer</label>
                    <input type="text" name="device[serial_number]" placeholder="z.B. SN-12345"
                        value="{{ old('device.serial_number') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">MTK Ablauf</label>
                    <input type="date" name="device[mtk_ablauf]"
                        value="{{ old('device.mtk_ablauf') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">STK Ablauf</label>
                    <input type="date" name="device[stk_ablauf]"
                        value="{{ old('device.stk_ablauf') }}"
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                </div>
            </div>
            <p class="px-4 pb-3 text-xs text-gray-400">
                Erstes Exemplar — weitere können nach dem Speichern auf der Detailseite hinzugefügt werden.
            </p>
        </div>
    </div>

</div>

<script>
(function () {
    const checkbox        = document.getElementById('is_device');
    const consumableBlock = document.getElementById('consumable-fields');
    const deviceBlock     = document.getElementById('device-fields');
    const unitInput       = consumableBlock.querySelector('[name="unit"]');

    function toggle() {
        const isDevice = checkbox.checked;
        consumableBlock.classList.toggle('hidden', isDevice);
        deviceBlock.classList.toggle('hidden', !isDevice);
        unitInput.required = !isDevice;
    }

    checkbox.addEventListener('change', toggle);
    toggle(); // init
})();
</script>
