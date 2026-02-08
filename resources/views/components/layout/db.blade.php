<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body
    class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center overflow-hidden w-full h-screen transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0 gap-4 lg:flex-row flex-col">
    {{-- List of database tables for current connection --}}
    <aside class="flex flex-col sticky top-6 self-start h-full border-r border-[#e3e3e0] dark:border-[#3E3E3A] pr-4">
        <a href="/" class="hover:text-blue-600 hover:underline dark:text-gray-400">{{ '<-' }}</a>
        <hr class="my-4 border-white" />
        <div class="flex flex-col gap-y-2">
            <a href="{{ route('db.home') }}" @class([
                'hover:text-blue-600 dark:text-gray-400',
                'font-semibold text-blue-500' => request()->routeIs('db.home'),
            ])>Home</a>
            <a href="{{ route('db.sql') }}" @class([
                'hover:text-blue-600 dark:text-gray-400',
                'font-semibold text-blue-500' => request()->routeIs('db.sql'),
            ])>SQL</a>
        </div>
        <hr class="my-4 border-white" />
        <div class="flex flex-col gap-1">
            <h2 class="font-semibold mb-2 dark:text-white">Tables</h2>
            @foreach ($tables as $table)
                @isset($selectedTable)
                    <a href="{{ route('db.table', $table['name']) }}" @class([
                        'hover:text-blue-600 dark:text-gray-400',
                        'font-semibold text-blue-500' => $table['name'] === $selectedTable,
                        'text-gray-500' => $table['name'] !== $selectedTable,
                    ])>
                        {{ $table['name'] }}
                    </a>
                @else
                    <a href="{{ route('db.table', $table['name']) }}" @class(['hover:text-blue-600 dark:text-gray-400'])>
                        {{ $table['name'] }}
                    </a>
                @endisset
            @endforeach
        </div>
    </aside>
    <main
        class="flex flex-col text-[13px] leading-5 flex-1 p-6 pb-12 lg:p-20 dark:text-[#EDEDEC] m-3 overflow-y-auto overflow-x-auto">
        {{ $slot }}
    </main>
</body>

</html>
