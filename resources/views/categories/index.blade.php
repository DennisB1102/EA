<x-app-layout>
    @section('title', 'Kategorien')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Kategorien</h1>
        <a href="{{ route('categories.create') }}" class="px-4 py-2 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">+ Neu</a>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Name</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Beschreibung</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Materialien</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($categories as $cat)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full" style="background:{{ $cat->color }}"></span>
                            <span class="font-medium text-gray-900">{{ $cat->name }}</span>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $cat->description ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $cat->materials_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('categories.edit', $cat) }}" class="text-xs text-gray-400 hover:text-cd-red">Bearbeiten</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Keine Kategorien vorhanden.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
