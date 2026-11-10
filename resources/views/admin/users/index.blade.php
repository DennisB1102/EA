<x-app-layout>
    @section('title', 'Benutzerverwaltung')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Benutzerverwaltung</h1>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">+ Neu</a>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Name</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">E-Mail</th>
                    <th class="text-left px-4 py-3 font-medium text-gray-600">Rolle</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $u)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $u->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $u->email }}</td>
                    <td class="px-4 py-3">
                        @php
                        $role = $u->roles->first()?->name;
                        $badge = ['admin' => 'bg-cd-red/10 text-cd-red', 'materialwart' => 'bg-blue-100 text-blue-800', 'user' => 'bg-gray-100 text-gray-700'];
                        @endphp
                        @if($role)
                        <span class="px-2 py-0.5 rounded text-xs font-medium {{ $badge[$role] ?? 'bg-gray-100 text-gray-700' }}">{{ $role }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right flex justify-end gap-3">
                        <a href="{{ route('admin.users.edit', $u) }}" class="text-xs text-gray-400 hover:text-cd-red">Bearbeiten</a>
                        @if($u->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Benutzer löschen?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-cd-red/50 hover:text-cd-red">Löschen</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">Keine Benutzer.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3">{{ $users->links() }}</div>
    </div>
</x-app-layout>
