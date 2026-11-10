<x-app-layout>
    @section('title', $equipment->name)
    <div class="mb-6 flex justify-between items-start">
        <div>
            <a href="{{ route('vehicles.show', $vehicle) }}" class="text-sm text-gray-500">← {{ $vehicle->name }}</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $equipment->name }}</h1>
            <span class="inline-block text-xs px-2 py-0.5 rounded-full font-medium mt-1
                @if($equipment->type === 'notfallrucksack') bg-orange-50 text-orange-700
                @elseif($equipment->type === 'medizingeraet') bg-blue-50 text-blue-700
                @elseif($equipment->type === 'trage') bg-purple-50 text-purple-700
                @else bg-gray-100 text-gray-500
                @endif">
                {{ $equipment->getTypeLabel() }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            @if($equipment->type === 'notfallrucksack')
            <a href="{{ route('vehicles.equipment.protocols.create', [$vehicle, $equipment]) }}"
               class="px-3 py-1.5 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">
                + Protokoll
            </a>
            <a href="{{ route('vehicles.equipment.protocols.index', [$vehicle, $equipment]) }}"
               class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm text-gray-600 hover:text-cd-red">
                Protokolle
            </a>
            @endif
            <a href="{{ route('vehicles.equipment.edit', [$vehicle, $equipment]) }}" class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm text-gray-600 hover:text-cd-red">Bearbeiten</a>
        </div>
    </div>

    @if($equipment->description)
        <p class="text-sm text-gray-500 mb-6">{{ $equipment->description }}</p>
    @endif

    {{-- ── Zugewiesene Geräte ──────────────────────────────────────────────── --}}
    <div class="mb-6">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-lg font-semibold text-gray-900">Geräte</h2>
            <button onclick="document.getElementById('assign-device-form').classList.toggle('hidden')"
                    class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                + Gerät zuweisen
            </button>
        </div>

        {{-- Zuweisung --}}
        <div id="assign-device-form" class="hidden mb-3 bg-blue-50 border border-blue-100 rounded-xl p-4">
            @if($availableDevices->isEmpty())
                <p class="text-sm text-gray-500">Keine freien Geräte verfügbar. Geräte können unter <a href="{{ route('materials.index') }}" class="text-cd-red underline">Materialien</a> angelegt werden.</p>
            @else
            <form method="POST" class="flex flex-wrap gap-3 items-end">
                @csrf
                <div class="flex-1 min-w-48">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Gerät auswählen</label>
                    <select name="instance_id" id="instance_select" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" required>
                        <option value="">— bitte wählen —</option>
                        @foreach($availableDevices as $typeName => $instances)
                            <optgroup label="{{ $typeName }}">
                                @foreach($instances as $inst)
                                <option value="{{ $inst->id }}">
                                    {{ $inst->serial_number ?: 'Keine SN' }}
                                    @if($inst->mtk_ablauf) · MTK {{ $inst->mtk_ablauf->format('d.m.Y') }}@endif
                                    @if($inst->stk_ablauf) · STK {{ $inst->stk_ablauf->format('d.m.Y') }}@endif
                                </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                {{-- dynamische Aktion per JS --}}
                <button type="button" onclick="submitAssign({{ $vehicle->id }}, {{ $equipment->id }})"
                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 font-medium">
                    Zuweisen
                </button>
            </form>
            <script>
            function submitAssign(vehicleId, equipmentId) {
                const sel = document.getElementById('instance_select');
                if (!sel.value) return;
                const form = sel.closest('form');
                form.action = `/EA/vehicles/${vehicleId}/equipment/${equipmentId}/devices/${sel.value}/assign`;
                form.submit();
            }
            </script>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            @if($equipment->assignedDevices->isEmpty())
                <p class="px-4 py-6 text-center text-sm text-gray-400">Noch keine Geräte zugewiesen.</p>
            @else
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Typ</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Seriennummer</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">MTK Ablauf</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">STK Ablauf</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($equipment->assignedDevices as $inst)
                    @php $mtkS = $inst->mtkStatus(); $stkS = $inst->stkStatus(); @endphp
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $inst->material->name }}</td>
                        <td class="px-4 py-3 font-mono text-gray-700">{{ $inst->serial_number ?: '—' }}</td>
                        <td class="px-4 py-3">
                            @if($inst->mtk_ablauf)
                            <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full
                                {{ $mtkS === 'expired' ? 'bg-red-100 text-red-700' : ($mtkS === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                {{ $mtkS === 'expired' ? '✗' : ($mtkS === 'warning' ? '⚠' : '✓') }}
                                {{ $inst->mtk_ablauf->format('d.m.Y') }}
                            </span>
                            @else <span class="text-gray-300 text-xs">—</span> @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($inst->stk_ablauf)
                            <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full
                                {{ $stkS === 'expired' ? 'bg-red-100 text-red-700' : ($stkS === 'warning' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                {{ $stkS === 'expired' ? '✗' : ($stkS === 'warning' ? '⚠' : '✓') }}
                                {{ $inst->stk_ablauf->format('d.m.Y') }}
                            </span>
                            @else <span class="text-gray-300 text-xs">—</span> @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('equipment.devices.unassign', [$vehicle, $equipment, $inst]) }}"
                                  onsubmit="return confirm('Gerät vom Rucksack entfernen?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-gray-400 hover:text-red-600">Entfernen</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    {{-- Beladung --}}
    <div class="flex justify-between items-center mb-3">
        <h2 class="text-lg font-semibold text-gray-900">Beladung</h2>
        <a href="{{ route('vehicles.equipment.inventory.create', [$vehicle, $equipment]) }}" class="px-3 py-1.5 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">+ Material</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Material</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Kategorie</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Ist / Soll</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Verfallsdatum</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($equipment->inventory as $inv)
                <tr class="{{ $inv->isShort() ? 'bg-red-50/30' : '' }}">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $inv->material->name }}</td>
                    <td class="px-4 py-3 text-gray-500">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full" style="background:{{ $inv->material->category->color }}"></span>
                            {{ $inv->material->category->name }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="{{ $inv->isShort() ? 'text-red-600 font-semibold' : 'text-gray-700' }}">
                            {{ $inv->current_quantity }}
                        </span>
                        <span class="text-gray-400"> / {{ $inv->target_quantity }} {{ $inv->material->unit }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-400">{{ $inv->expiry_date?->format('d.m.Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('vehicles.equipment.inventory.edit', [$vehicle, $equipment, $inv]) }}" class="text-xs text-gray-400 hover:text-cd-red mr-3">Bearbeiten</a>
                        <form method="POST" action="{{ route('vehicles.equipment.inventory.destroy', [$vehicle, $equipment, $inv]) }}" class="inline" onsubmit="return confirm('Eintrag löschen?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-gray-400 hover:text-red-600">Entfernen</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">Noch kein Material verlastet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
