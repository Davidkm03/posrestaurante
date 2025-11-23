@props([
    'align' => 'right',
    'width' => '48',
    'contentClasses' => 'py-1 bg-white',
])

@php
$alignmentClasses = match ($align) {
    'left' => 'left-0 origin-top-left',
    'right' => 'right-0 origin-top-right',
    'center' => 'left-1/2 -translate-x-1/2 origin-top',
    default => 'right-0 origin-top-right',
};

$widthClasses = match ($width) {
    '48' => 'w-48',
    '56' => 'w-56',
    '64' => 'w-64',
    '72' => 'w-72',
    '80' => 'w-80',
    'auto' => 'w-auto',
    default => 'w-48',
};
@endphp

<div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
    <!-- Trigger -->
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <!-- Dropdown Content -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-50 mt-2 {{ $widthClasses }} {{ $alignmentClasses }} rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
        style="display: none;"
        @click="open = false"
    >
        <div class="rounded-md {{ $contentClasses }}">
            {{ $slot }}
        </div>
    </div>
</div>
