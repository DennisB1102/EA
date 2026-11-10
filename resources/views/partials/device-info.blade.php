{{-- Geräte-Prüfblock im Protokollformular --}}
{{-- $assignedDevices: Collection von DeviceInstance --}}
@if($assignedDevices->isNotEmpty())
<div class="bg-white rounded-xl border border-blue-100 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-2.5 border-b border-blue-100 bg-blue-600">
        <h3 class="font-semibold text-white text-sm">Geräte</h3>
        <span class="text-xs text-white/70">{{ $assignedDevices->count() }} Gerät(e)</span>
    </div>
    <table class="w-full text-sm">
        <thead class="border-b border-gray-100">
            <tr>
                <th class="w-12 px-4 py-2 text-center text-xs font-medium text-gray-400">i.O.</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">Typ · Seriennummer</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-400">MTK / STK</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 w-44">Notiz</th>
                <th class="w-10 px-2 py-2"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($assignedDevices as $inst)
            @php $mtkS = $inst->mtkStatus(); $stkS = $inst->stkStatus(); $dfid = 'dev_photo_' . $inst->id; @endphp
            <tr class="hover:bg-blue-50/30 transition-colors">
                <td class="px-4 py-3 text-center">
                    <input type="checkbox"
                           name="devices[{{ $inst->id }}][is_ok]"
                           class="check-io w-4 h-4 accent-green-600 cursor-pointer"
                           value="1">
                </td>
                <td class="px-4 py-3">
                    <span class="font-medium text-gray-900">{{ $inst->material->name }}</span>
                    <span class="ml-2 font-mono text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">
                        {{ $inst->serial_number ?: 'Keine SN' }}
                    </span>
                    <span id="{{ $dfid }}_label" class="hidden ml-1 text-xs text-blue-600 font-medium"></span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex flex-wrap gap-1.5">
                        @if($inst->mtk_ablauf)
                        <span class="inline-flex items-center gap-1 text-xs font-medium px-1.5 py-0.5 rounded
                            {{ $mtkS === 'expired' ? 'bg-red-100 text-red-700' : ($mtkS === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                            MTK {{ $inst->mtk_ablauf->format('d.m.Y') }}
                            {{ $mtkS === 'expired' ? '✗' : ($mtkS === 'warning' ? '⚠' : '✓') }}
                        </span>
                        @endif
                        @if($inst->stk_ablauf)
                        <span class="inline-flex items-center gap-1 text-xs font-medium px-1.5 py-0.5 rounded
                            {{ $stkS === 'expired' ? 'bg-red-100 text-red-700' : ($stkS === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                            STK {{ $inst->stk_ablauf->format('d.m.Y') }}
                            {{ $stkS === 'expired' ? '✗' : ($stkS === 'warning' ? '⚠' : '✓') }}
                        </span>
                        @endif
                        @if(!$inst->mtk_ablauf && !$inst->stk_ablauf)
                        <span class="text-xs text-gray-300">—</span>
                        @endif
                    </div>
                </td>
                <td class="px-4 py-3">
                    <input type="text"
                           name="devices[{{ $inst->id }}][notes]"
                           placeholder="Notiz…"
                           class="w-full border border-gray-200 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-cd-red/30">
                </td>
                <td class="px-2 py-3 text-center">
                    <button type="button" title="Foto anhängen"
                            onclick="document.getElementById('{{ $dfid }}').click()"
                            class="text-gray-300 hover:text-blue-500 transition-colors text-base leading-none">📷</button>
                    <input type="file" id="{{ $dfid }}"
                           name="devices[{{ $inst->id }}][photo]"
                           accept="image/*" capture="environment"
                           class="hidden"
                           onchange="photoSelected(this, '{{ $dfid }}_label')">
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
