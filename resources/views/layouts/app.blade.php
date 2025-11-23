<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'POS Restaurant') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased">
    <div x-data="{ sidebarOpen: false }" class="min-h-full">
        <!-- Mobile sidebar overlay -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-40 lg:hidden"
             @click="sidebarOpen = false">
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
        </div>

        <!-- Sidebar -->
        @include('partials.sidebar')

        <!-- Main content -->
        <div class="lg:pl-64">
            <!-- Top navbar -->
            @include('partials.header')

            <main class="py-6">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <!-- Flash Messages -->
                    @if (session('success'))
                        <x-alert type="success" class="mb-4">
                            {{ session('success') }}
                        </x-alert>
                    @endif

                    @if (session('error'))
                        <x-alert type="error" class="mb-4">
                            {{ session('error') }}
                        </x-alert>
                    @endif

                    <!-- Page Header -->
                    @isset($header)
                        <div class="mb-6">
                            {{ $header }}
                        </div>
                    @endisset

                    <!-- Page Content -->
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
