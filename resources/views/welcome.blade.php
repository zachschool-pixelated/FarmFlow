<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'FarmFlow') }} - Supply Management</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            if (document.documentElement.classList.contains('dark')) {
                localStorage.theme = 'dark';
            } else {
                localStorage.theme = 'light';
            }
        }
    </script>
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100 flex flex-col min-h-screen">
    
    <header class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <x-application-logo class="h-8 w-8 text-farm-600 dark:text-farm-400" />
            <span class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">FarmFlow</span>
        </div>
        
        <nav class="flex items-center gap-4">
            <button onclick="toggleDarkMode()" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors" aria-label="Toggle Dark Mode">
                <svg class="w-5 h-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                <svg class="w-5 h-5 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
            </button>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-farm-700 hover:text-farm-800 dark:text-farm-400 dark:hover:text-farm-300 transition-colors">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white transition-colors">Log in</a>
                @endauth
            @endif
        </nav>
    </header>

    <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-3xl w-full text-center space-y-8 animate-fade-in">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-farm-100 dark:bg-farm-900/30 text-farm-600 dark:text-farm-400 mb-6 shadow-sm">
                <x-application-logo class="h-10 w-10 text-farm-600 dark:text-farm-400" />
            </div>
            
            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                Farm <span class="text-farm-600 dark:text-farm-400">Supply Management</span>
            </h1>
            
            <p class="text-lg sm:text-xl text-gray-500 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed">
                FarmFlow connects administrators and suppliers in a single, unified platform. Manage stock requests, track inventory movements, and oversee your entire procurement pipeline efficiently.
            </p>
            
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-8 py-3.5 border border-transparent text-base font-medium rounded-xl text-white bg-farm-600 hover:bg-farm-700 shadow-sm transition-all hover:shadow hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-farm-500 w-full sm:w-auto">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-3.5 border border-transparent text-base font-medium rounded-xl text-white bg-farm-600 hover:bg-farm-700 shadow-sm transition-all hover:shadow hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-farm-500 w-full sm:w-auto">
                        Sign In to Portal
                    </a>
                @endauth
            </div>
        </div>
    </main>

    <footer class="w-full border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 py-8 text-center text-sm text-gray-500 dark:text-gray-400 mt-auto">
        <p>&copy; {{ date('Y') }} FarmFlow. All rights reserved.</p>
    </footer>

</body>
</html>
