<x-qr-layout :title="'Prüfprotokoll ' . $equipment->name">

    <div class="mb-5">
        <h1 class="text-2xl font-bold text-gray-900">Neues Prüfprotokoll</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $equipment->name }} · Außenprüfung</p>
    </div>

    <form method="POST" action="{{ route('qr.store', $equipment) }}" id="protocol-form">
        @csrf

        <div class="flex flex-col lg:flex-row gap-5 items-start">

            {{-- ── Materialien ──────────────────────────────────────── --}}
            <div class="flex-1 min-w-0 space-y-4">
                @foreach($inventory as $category => $items)
                @php $catColor = $items->first()->material->category->color ?? '#6B7280'; @endphp
                <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-2.5 border-b border-black/10"
                         style="background-color: {{ $catColor }}">
                        <h3 class="font-semibold text-white text-sm">{{ $category }}</h3>
                        <span class="text-xs text-white/70">{{ $items->count() }} Pos.</span>
                    </div>
                    <table class="w-full text-sm">
                        <thead class="border-b border-gray-100">
                            <tr>
                                <th class="w-12 px-4 py-2 text-center text-xs font-medium text-gray-400">i.O.</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Material</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-400 w-28">Ist / Soll</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 w-44">Notiz</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($items as $inv)
                            <tr class="hover:bg-gray-50/50 transition-colors" data-row>
                                <td class="px-4 py-2.5 text-center">
                                    <input type="checkbox"
                                           name="items[{{ $inv->material_id }}][is_ok]"
                                           class="check-io w-4 h-4 accent-green-600 cursor-pointer"
                                           value="1">
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="font-medium text-gray-900">{{ $inv->material->name }}</span>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <input type="number"
                                               name="items[{{ $inv->material_id }}][current_quantity]"
                                               min="0" value="{{ $inv->target_quantity }}"
                                               class="w-14 border border-gray-200 rounded px-1.5 py-1 text-sm text-center focus:outline-none focus:ring-1 focus:ring-cd-red/30">
                                        <span class="text-gray-300">/</span>
                                        <span class="text-xs text-gray-400 w-8 text-left">{{ $inv->target_quantity }}<span class="ml-0.5">{{ $inv->material->unit }}</span></span>
                                    </div>
                                </td>
                                <td class="px-4 py-2.5">
                                    <input type="text"
                                           name="items[{{ $inv->material_id }}][notes]"
                                           placeholder="Notiz…"
                                           class="w-full border border-gray-200 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-cd-red/30">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endforeach

                @include('partials.device-info', ['assignedDevices' => $assignedDevices])
            </div>

            {{-- ── Sidebar ──────────────────────────────────────────── --}}
            <div class="w-full lg:w-72 shrink-0 space-y-4 lg:sticky lg:top-6">

                <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                        <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Prüfungsdetails</h2>
                    </div>
                    <div class="p-4 space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Datum <span class="text-red-500">*</span></label>
                            <input type="date" name="checked_at" value="{{ now()->format('Y-m-d') }}" required
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cd-red/30">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Geprüft durch <span class="text-red-500">*</span></label>
                            <input type="text" name="checked_by" required placeholder="Name des Prüfers"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cd-red/30">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Allgemeine Notizen</label>
                            <textarea name="notes" rows="2" placeholder="Optionale Bemerkungen…"
                                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cd-red/30 resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                        <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Fortschritt</h2>
                    </div>
                    <div class="p-4">
                        <div class="flex items-end justify-between mb-2">
                            <span class="text-2xl font-bold text-gray-900" id="checked-count">0</span>
                            <span class="text-sm text-gray-400 mb-0.5">/ {{ $inventory->flatten()->count() }} geprüft</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div id="progress-bar" class="bg-green-500 h-2 rounded-full transition-all duration-200" style="width: 0%"></div>
                        </div>
                        <button type="button" onclick="checkAll(true)"
                                class="mt-3 w-full px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 font-medium">
                            ✓ Alle i.O. markieren
                        </button>
                        <button type="button" onclick="checkAll(false)"
                                class="mt-2 w-full px-3 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:border-gray-300">
                            Alle zurücksetzen
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                        <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Unterschrift</h2>
                    </div>
                    <div class="p-4">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg overflow-hidden bg-gray-50" style="touch-action: none;">
                            <canvas id="sig-canvas" width="600" height="160"
                                    class="w-full cursor-crosshair block"></canvas>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <span id="sig-hint" class="text-xs text-gray-400">Hier unterschreiben</span>
                            <button type="button" onclick="clearSignature()"
                                    class="text-xs text-gray-400 hover:text-red-600 border border-gray-200 rounded px-2 py-0.5">
                                Löschen
                            </button>
                        </div>
                        <input type="hidden" name="signature_data" id="signature-data">
                    </div>
                </div>

                <div class="space-y-2">
                    <button type="submit"
                            class="w-full px-4 py-2.5 bg-cd-red text-white rounded-xl text-sm font-semibold hover:bg-[#b5010f]">
                        Protokoll speichern
                    </button>
                </div>

            </div>
        </div>
    </form>

    <script>
    const canvas = document.getElementById('sig-canvas');
    const ctx    = canvas.getContext('2d');
    let drawing  = false;
    let hasSig   = false;

    function getPos(e) {
        const r = canvas.getBoundingClientRect();
        const src = e.touches ? e.touches[0] : e;
        return {
            x: (src.clientX - r.left) * (canvas.width  / r.width),
            y: (src.clientY - r.top)  * (canvas.height / r.height),
        };
    }

    canvas.addEventListener('mousedown',  e => { drawing = true; ctx.beginPath(); const p = getPos(e); ctx.moveTo(p.x, p.y); });
    canvas.addEventListener('mousemove',  e => { if (!drawing) return; const p = getPos(e); ctx.lineWidth = 2.5; ctx.lineCap = 'round'; ctx.lineJoin = 'round'; ctx.strokeStyle = '#1e293b'; ctx.lineTo(p.x, p.y); ctx.stroke(); hasSig = true; document.getElementById('sig-hint').textContent = ''; });
    canvas.addEventListener('mouseup',    () => drawing = false);
    canvas.addEventListener('mouseleave', () => drawing = false);

    canvas.addEventListener('touchstart',  e => { e.preventDefault(); drawing = true; ctx.beginPath(); const p = getPos(e); ctx.moveTo(p.x, p.y); }, { passive: false });
    canvas.addEventListener('touchmove',   e => { e.preventDefault(); if (!drawing) return; const p = getPos(e); ctx.lineWidth = 2.5; ctx.lineCap = 'round'; ctx.lineJoin = 'round'; ctx.strokeStyle = '#1e293b'; ctx.lineTo(p.x, p.y); ctx.stroke(); hasSig = true; document.getElementById('sig-hint').textContent = ''; }, { passive: false });
    canvas.addEventListener('touchend',    () => drawing = false);

    function clearSignature() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasSig = false;
        document.getElementById('sig-hint').textContent = 'Hier unterschreiben';
    }

    const total      = document.querySelectorAll('.check-io').length;
    const countEl    = document.getElementById('checked-count');
    const progressEl = document.getElementById('progress-bar');

    function updateProgress() {
        const checked = document.querySelectorAll('.check-io:checked').length;
        countEl.textContent = checked;
        progressEl.style.width = (checked / total * 100) + '%';
    }

    document.querySelectorAll('.check-io').forEach(cb => cb.addEventListener('change', updateProgress));

    function checkAll(state) {
        document.querySelectorAll('.check-io').forEach(cb => cb.checked = state);
        updateProgress();
    }

    document.getElementById('protocol-form').addEventListener('submit', function () {
        if (hasSig) {
            document.getElementById('signature-data').value = canvas.toDataURL('image/png');
        }
    });
    </script>

</x-qr-layout>
