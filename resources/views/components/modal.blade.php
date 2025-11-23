@props([
    'name' => 'modal',
    'title' => '',
    'maxWidth' => 'lg',
    'closeable' => true,
])

@php
$maxWidthClasses = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    '3xl' => 'sm:max-w-3xl',
    '4xl' => 'sm:max-w-4xl',
    '5xl' => 'sm:max-w-5xl',
    'full' => 'sm:max-w-full sm:mx-4',
];
@endphp

<div
    x-data="{ show: false }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') show = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
>
    <!-- Backdrop -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @if($closeable) @click="show = false" @endif
    ></div>

    <!-- Modal Panel -->
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div
            x-show="show"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full {{ $maxWidthClasses[$maxWidth] ?? $maxWidthClasses['lg'] }}"
            @click.stop
        >
            <!-- Header -->
            @if($title)
                <div class="bg-white px-4 py-4 sm:px-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                        @if($closeable)
                            <button
                                type="button"
                                @click="show = false"
                                class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <span class="sr-only">Cerrar</span>
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Body -->
            <div class="bg-white px-4 py-5 sm:p-6">
                {{ $slot }}
            </div>

            <!-- Footer -->
            @isset($footer)
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
