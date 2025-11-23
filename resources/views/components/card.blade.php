@props([
    'title' => null,
    'subtitle' => null,
    'footer' => null,
    'padding' => true,
    'shadow' => true,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg overflow-hidden ' . ($shadow ? 'shadow' : '')]) }}>
    @if($title || isset($header))
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            @isset($header)
                {{ $header }}
            @else
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                        @if($subtitle)
                            <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
                        @endif
                    </div>
                    @isset($actions)
                        <div class="flex items-center gap-2">
                            {{ $actions }}
                        </div>
                    @endisset
                </div>
            @endisset
        </div>
    @endif

    <div class="{{ $padding ? 'px-4 py-5 sm:p-6' : '' }}">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="bg-gray-50 px-4 py-4 sm:px-6 border-t border-gray-200">
            {{ $footer }}
        </div>
    @endisset
</div>
