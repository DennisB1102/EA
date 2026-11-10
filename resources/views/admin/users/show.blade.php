<x-app-layout>
    @section('title', $user->name)
    <div class="max-w-md">
        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500">← Zurück</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2 mb-4">{{ $user->name }}</h1>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-sm space-y-3">
            <div class="flex justify-between"><span class="text-gray-500">E-Mail</span><span>{{ $user->email }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Rolle</span><span>{{ $user->roles->first()?->name ?? '—' }}</span></div>
        </div>
    </div>
</x-app-layout>
