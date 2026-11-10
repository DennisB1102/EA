<x-app-layout>
    @section('title', 'Benutzer bearbeiten')
    <div class="max-w-md">
        <div class="mb-6">
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Zurück</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $user->name }} bearbeiten</h1>
        </div>
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mb-4">
            @csrf @method('PUT')
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-800">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">E-Mail *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Neues Passwort (optional)</label>
                <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Passwort bestätigen</label>
                <input type="password" name="password_confirmation" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">Speichern</button>
        </form>

        <form method="POST" action="{{ route('admin.users.role', $user) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            @csrf @method('PATCH')
            <h2 class="font-semibold text-gray-700">Rolle ändern</h2>
            <div>
                <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    @foreach($roles as $r)
                    <option value="{{ $r->name }}" {{ $user->hasRole($r->name) ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-gray-700 text-white rounded-lg text-sm font-medium hover:bg-gray-800">Rolle speichern</button>
        </form>
    </div>
</x-app-layout>
