<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'FarmFlow') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Dark Mode FOUC Prevention -->
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased transition-colors duration-200">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
            <div class="flex flex-col items-center">
                <a href="/" class="flex items-center gap-3">
                    <x-application-logo class="w-12 h-12 fill-current text-farm-600 dark:text-farm-400" />
                    <span class="text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white">FarmFlow</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white dark:bg-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-gray-900/50 overflow-hidden sm:rounded-2xl border border-gray-100 dark:border-gray-700 transition-colors duration-200">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
