<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body class="transition-colors duration-500 dark:bg-neutral-800">
    <!-- Header menu -->
    <x-nav-menu :user="Auth::user()" />

    <!-- side menu -->
    <x-aside />

    <main class="relative leading-[1.6]">
        <section
            class="mx-auto mb-[64px] mt-12 pb-[64px] min-[576px]:max-w-[540px] min-[768px]:max-w-[720px] min-[992px]:max-w-[960px] min-[1200px]:max-w-[1140px] min-[1400px]:max-w-[1320px]">
            {{ $slot }}
        </section>
    </main>

    @stack('modals')

    @livewireScripts

    @stack('scripts')
</body>

</html>
