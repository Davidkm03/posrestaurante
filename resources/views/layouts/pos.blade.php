<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1f2937">

    <title>{{ $title ?? 'POS' }} - {{ config('app.name', 'POS Restaurant') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        /* POS specific styles for touch optimization */
        .pos-btn {
            @apply min-h-[48px] touch-manipulation select-none;
        }
        .pos-product-card {
            @apply min-h-[100px] touch-manipulation select-none transition-transform active:scale-95;
        }
        /* Hide scrollbar but keep functionality */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="h-full bg-gray-900 text-white overflow-hidden">
    <div class="h-screen flex flex-col">
        <!-- POS Header -->
        @include('pos.partials.header')

        <!-- Main POS Content -->
        <div class="flex-1 flex overflow-hidden">
            {{ $slot }}
        </div>

        <!-- POS Footer/Status Bar -->
        @include('pos.partials.footer')
    </div>

    <!-- Modals Container -->
    <div id="pos-modals">
        @stack('modals')
    </div>

    @livewireScripts

    <script>
        // Prevent zoom on double tap for touch devices
        document.addEventListener('touchstart', function(event) {
            if (event.touches.length > 1) {
                event.preventDefault();
            }
        }, { passive: false });

        let lastTouchEnd = 0;
        document.addEventListener('touchend', function(event) {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
    </script>

    @stack('scripts')
</body>
</html>
