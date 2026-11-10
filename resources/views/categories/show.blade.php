<x-app-layout>
    @section('title', $category->name)
    <div class="max-w-2xl">
        <a href="{{ route('categories.index') }}" class="text-sm text-gray-500">← Zurück</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2 mb-4">{{ $category->name }}</h1>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            @forelse($category->materials as $m)
            <div class="flex justify-between py-2 border-b border-gray-50 text-sm last:border-0">
                <a href="{{ route('materials.show', $m) }}" class="text-gray-900 hover:text-cd-red">{{ $m->name }}</a>
                <span class="text-gray-400">{{ $m->totalStock() }} {{ $m->unit }}</span>
            </div>
            @empty
            <p class="text-gray-400 text-sm">Keine Materialien in dieser Kategorie.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
