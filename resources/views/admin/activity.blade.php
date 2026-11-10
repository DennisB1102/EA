<x-app-layout>
    @section('title', 'Aktivitätslog')

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Aktivitätslog</h1>
        <span class="text-sm text-gray-400">{{ $logs->total() }} Einträge gesamt</span>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 w-36">Zeitpunkt</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 w-28">Benutzer</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 w-24">Aktion</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600 w-32">Objekt</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Änderungen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logs as $log)
                @php
                    $event     = $log->event ?? $log->description;
                    $eventColor = match($event) {
                        'created' => 'bg-green-100 text-green-700',
                        'updated' => 'bg-blue-100 text-blue-700',
                        'deleted' => 'bg-red-100 text-red-700',
                        default   => 'bg-gray-100 text-gray-600',
                    };
                    $eventLabel = match($event) {
                        'created' => 'Erstellt',
                        'updated' => 'Geändert',
                        'deleted' => 'Gelöscht',
                        default   => $event,
                    };
                    $subjectType = $log->subject_type ? class_basename($log->subject_type) : '—';
                    $changes = $log->properties['attributes'] ?? [];
                    $old     = $log->properties['old'] ?? [];
                @endphp
                <tr class="hover:bg-gray-50/50">
                    <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">
                        {{ $log->created_at->format('d.m.Y H:i') }}
                    </td>
                    <td class="px-4 py-3 text-gray-700 text-xs">
                        {{ $log->causer?->name ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $eventColor }}">
                            {{ $eventLabel }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs">
                        {{ $subjectType }}
                        @if($log->subject_id)
                            <span class="text-gray-400">#{{ $log->subject_id }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs">
                        @if($event === 'updated' && !empty($old))
                            <div class="flex flex-wrap gap-2">
                                @foreach($old as $field => $oldVal)
                                <span class="text-gray-500">
                                    <span class="font-medium text-gray-700">{{ $field }}:</span>
                                    <span class="line-through text-red-500">{{ is_bool($oldVal) ? ($oldVal ? 'ja' : 'nein') : ($oldVal ?? '—') }}</span>
                                    →
                                    <span class="text-green-700">{{ is_bool($changes[$field] ?? null) ? (($changes[$field] ?? false) ? 'ja' : 'nein') : ($changes[$field] ?? '—') }}</span>
                                </span>
                                @endforeach
                            </div>
                        @elseif($event === 'created' && !empty($changes))
                            <div class="flex flex-wrap gap-2">
                                @foreach(array_slice($changes, 0, 5, true) as $field => $val)
                                <span class="text-gray-500">
                                    <span class="font-medium text-gray-700">{{ $field }}:</span>
                                    {{ is_bool($val) ? ($val ? 'ja' : 'nein') : ($val ?? '—') }}
                                </span>
                                @endforeach
                                @if(count($changes) > 5)
                                    <span class="text-gray-400">+{{ count($changes) - 5 }} weitere</span>
                                @endif
                            </div>
                        @else
                            <span class="text-gray-400">{{ $log->description }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">Keine Aktivitäten vorhanden.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-50">{{ $logs->links() }}</div>
    </div>
</x-app-layout>
