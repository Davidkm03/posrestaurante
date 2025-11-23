@props([
    'title' => '',
    'value' => '',
    'icon' => null,
    'trend' => null,
    'trendValue' => null,
    'color' => 'blue',
])

@php
$colors = [
    'blue' => ['bg' => 'bg-blue-500', 'light' => 'bg-blue-100', 'text' => 'text-blue-600'],
    'green' => ['bg' => 'bg-green-500', 'light' => 'bg-green-100', 'text' => 'text-green-600'],
    'yellow' => ['bg' => 'bg-yellow-500', 'light' => 'bg-yellow-100', 'text' => 'text-yellow-600'],
    'red' => ['bg' => 'bg-red-500', 'light' => 'bg-red-100', 'text' => 'text-red-600'],
    'purple' => ['bg' => 'bg-purple-500', 'light' => 'bg-purple-100', 'text' => 'text-purple-600'],
    'gray' => ['bg' => 'bg-gray-500', 'light' => 'bg-gray-100', 'text' => 'text-gray-600'],
];
$colorSet = $colors[$color] ?? $colors['blue'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow p-6']) }}>
    <div class="flex items-center">
        @if($icon)
            <div class="flex-shrink-0">
                <div class="w-12 h-12 {{ $colorSet['light'] }} rounded-lg flex items-center justify-center">
                    <span class="{{ $colorSet['text'] }}">{!! $icon !!}</span>
                </div>
            </div>
        @endif

        <div class="{{ $icon ? 'ml-4' : '' }} flex-1">
            <p class="text-sm font-medium text-gray-500 truncate">{{ $title }}</p>
            <div class="flex items-baseline">
                <p class="text-2xl font-semibold text-gray-900">{{ $value }}</p>
                @if($trend && $trendValue)
                    <p class="ml-2 flex items-baseline text-sm font-semibold {{ $trend === 'up' ? 'text-green-600' : 'text-red-600' }}">
                        @if($trend === 'up')
                            <svg class="h-4 w-4 flex-shrink-0 self-center" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            <svg class="h-4 w-4 flex-shrink-0 self-center" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                        <span class="sr-only">{{ $trend === 'up' ? 'Incrementó' : 'Decrementó' }} por</span>
                        {{ $trendValue }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    @isset($footer)
        <div class="mt-4 pt-4 border-t border-gray-100">
            {{ $footer }}
        </div>
    @endisset
</div>
