<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="refresh" content="30">

    <title>Cocina - {{ config('app.name', 'POS Restaurant') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        /* Kitchen Display specific styles */
        .order-card {
            @apply rounded-lg shadow-lg overflow-hidden;
        }
        .order-card.urgent {
            @apply ring-4 ring-red-500 animate-pulse;
        }
        .order-card.warning {
            @apply ring-2 ring-yellow-500;
        }
        .order-item {
            @apply py-2 border-b border-gray-700 last:border-0;
        }
        .order-item.completed {
            @apply line-through opacity-50;
        }
        /* Large text for kitchen visibility */
        .kitchen-text-lg {
            font-size: 1.25rem;
            line-height: 1.75rem;
        }
        .kitchen-text-xl {
            font-size: 1.5rem;
            line-height: 2rem;
        }
    </style>
</head>
<body class="h-full bg-gray-900 text-white">
    <div class="h-screen flex flex-col">
        <!-- Kitchen Header -->
        @include('kitchen.partials.header')

        <!-- Kitchen Display Content -->
        <div class="flex-1 overflow-hidden p-4">
            {{ $slot }}
        </div>

        <!-- Audio for notifications -->
        <audio id="notification-sound" preload="auto">
            <source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg">
        </audio>
    </div>

    @livewireScripts

    <script>
        // Auto-refresh orders via Livewire
        document.addEventListener('livewire:initialized', () => {
            setInterval(() => {
                Livewire.dispatch('refresh-orders');
            }, 10000);
        });

        // Play sound on new order
        window.addEventListener('new-order', () => {
            const sound = document.getElementById('notification-sound');
            if (sound) {
                sound.play().catch(() => {});
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
