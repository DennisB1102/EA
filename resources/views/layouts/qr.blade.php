<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Prüfprotokoll' }} – EA Tool</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 min-h-screen">

    {{-- Minimale Kopfleiste --}}
    <header class="bg-cd-navy text-white px-4 py-3 flex items-center gap-3">
        <div class="w-2 h-6 bg-cd-red rounded-sm shrink-0"></div>
        <div>
            <span class="font-bold text-base tracking-wide">EA Tool</span>
            <span class="text-white/50 mx-2">·</span>
            <span class="text-cd-yellow text-sm font-medium">{{ $title ?? 'Außenprüfung' }}</span>
        </div>
    </header>

    <main class="p-4 lg:p-6 max-w-5xl mx-auto">
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{ $slot }}
    </main>

</body>
</html>
