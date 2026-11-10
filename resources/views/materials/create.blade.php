<x-app-layout>
    @section('title', 'Material anlegen')
    <div class="max-w-2xl">
        <div class="mb-6">
            <a href="{{ route('materials.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Zurück</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Material anlegen</h1>
        </div>

        <form method="POST" action="{{ route('materials.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            @csrf
            @include('materials._form')
            <div class="pt-2">
                <button type="submit" class="px-6 py-2 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">Speichern</button>
            </div>
        </form>
    </div>
</x-app-layout>
