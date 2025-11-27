<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
            @apply rounded-xl shadow-lg overflow-hidden transition-all;
        }
        .order-card:hover {
            @apply shadow-xl;
        }
        .order-card.urgent {
            animation: urgentPulse 1s ease-in-out infinite;
        }
        @keyframes urgentPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            50% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
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
        /* Custom scrollbar */
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: transparent;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #4B5563;
            border-radius: 3px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #6B7280;
        }
    </style>
</head>
<body class="h-full bg-gray-900 text-white overflow-hidden">
    {{ $slot }}

    @livewireScripts
    @stack('scripts')
</body>
</html>
