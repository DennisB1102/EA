<x-app-layout>
    @section('title', 'Benutzer anlegen')
    <div class="max-w-md">
        <div class="mb-6">
            <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Zurück</a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Benutzer anlegen</h1>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            @csrf
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-800">
                @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">E-Mail *</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Passwort *</label>
                <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Passwort bestätigen *</label>
                <input type="password" name="password_confirmation" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rolle *</label>
                <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                    @foreach($roles as $r)
                    <option value="{{ $r->name }}" {{ old('role') == $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-cd-red text-white rounded-lg text-sm font-medium hover:bg-[#b5010f]">Benutzer anlegen</button>
        </form>
    </div>
</x-app-layout>
