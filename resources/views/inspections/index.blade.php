<x-app-layout>
    @section('title', 'Prüfprotokoll')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Prüfprotokoll</h1>
        <a href="{{ route('inspections.create') }}" class="px-4 py-2 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">+ Neue Prüfung</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Material</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Standort</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Ergebnis</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Prüfer</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Datum</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Nächste Prüfung</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($inspections as $ins)
                @php
                $colors = ['ok' => 'bg-green-100 text-green-800', 'defect' => 'bg-red-100 text-red-800', 'replaced' => 'bg-blue-100 text-blue-800'];
                $labels = ['ok' => 'OK', 'defect' => 'Defekt', 'replaced' => 'Ersetzt'];
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $ins->material->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $ins->location->name }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded text-xs font-medium {{ $colors[$ins->result] }}">{{ $labels[$ins->result] }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $ins->user->name }}</td>
                    <td class="px-4 py-3 text-gray-400">{{ $ins->created_at->format('d.m.Y') }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $ins->next_inspection_due?->format('d.m.Y') ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Keine Prüfungen vorhanden.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3">{{ $inspections->links() }}</div>
    </div>
</x-app-layout>
