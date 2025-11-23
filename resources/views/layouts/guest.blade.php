<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Login' }} - {{ config('app.name', 'POS Restaurant') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gradient-to-br from-blue-600 to-blue-800">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Logo -->
            <div class="flex justify-center">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-white">
                {{ config('app.name', 'POS Restaurant') }}
            </h2>
            @isset($subtitle)
                <p class="mt-2 text-center text-sm text-blue-200">
                    {{ $subtitle }}
                </p>
            @endisset
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-xl sm:rounded-lg sm:px-10">
                {{ $slot }}
            </div>
        </div>

        <p class="mt-8 text-center text-sm text-blue-200">
            &copy; {{ date('Y') }} {{ config('app.name', 'POS Restaurant') }}. Todos los derechos reservados.
        </p>
    </div>
</body>
</html>
