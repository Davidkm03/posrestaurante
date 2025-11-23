@props([
    'type' => 'info',
    'dismissible' => true,
    'title' => null,
])

@php
$types = [
    'info' => [
        'bg' => 'bg-blue-50',
        'border' => 'border-blue-400',
        'icon' => 'text-blue-400',
        'title' => 'text-blue-800',
        'text' => 'text-blue-700',
    ],
    'success' => [
        'bg' => 'bg-green-50',
        'border' => 'border-green-400',
        'icon' => 'text-green-400',
        'title' => 'text-green-800',
        'text' => 'text-green-700',
    ],
    'warning' => [
        'bg' => 'bg-yellow-50',
        'border' => 'border-yellow-400',
        'icon' => 'text-yellow-400',
        'title' => 'text-yellow-800',
        'text' => 'text-yellow-700',
    ],
    'error' => [
        'bg' => 'bg-red-50',
        'border' => 'border-red-400',
        'icon' => 'text-red-400',
        'title' => 'text-red-800',
        'text' => 'text-red-700',
    ],
];

$colors = $types[$type] ?? $types['info'];

$icons = [
    'info' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
    'error' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    {{ $attributes->merge(['class' => "rounded-lg border-l-4 p-4 {$colors['bg']} {$colors['border']}"]) }}
>
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 {{ $colors['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {!! $icons[$type] ?? $icons['info'] !!}
            </svg>
        </div>
        <div class="ml-3 flex-1">
            @if($title)
                <h3 class="text-sm font-medium {{ $colors['title'] }}">{{ $title }}</h3>
            @endif
            <div class="text-sm {{ $colors['text'] }} @if($title) mt-1 @endif">
                {{ $slot }}
            </div>
        </div>
        @if($dismissible)
            <div class="ml-auto pl-3">
                <button
                    type="button"
                    @click="show = false"
                    class="-mx-1.5 -my-1.5 inline-flex rounded-lg p-1.5 {{ $colors['text'] }} hover:{{ $colors['bg'] }} focus:outline-none focus:ring-2 focus:ring-offset-2"
                >
                    <span class="sr-only">Cerrar</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        @endif
    </div>
</div>
