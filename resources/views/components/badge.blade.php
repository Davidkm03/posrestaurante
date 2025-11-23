@props([
    'type' => 'default',
    'size' => 'md',
    'rounded' => 'full',
    'dot' => false,
])

@php
$types = [
    'default' => 'bg-gray-100 text-gray-800',
    'primary' => 'bg-blue-100 text-blue-800',
    'secondary' => 'bg-gray-100 text-gray-800',
    'success' => 'bg-green-100 text-green-800',
    'danger' => 'bg-red-100 text-red-800',
    'warning' => 'bg-yellow-100 text-yellow-800',
    'info' => 'bg-cyan-100 text-cyan-800',
    'purple' => 'bg-purple-100 text-purple-800',
    'pink' => 'bg-pink-100 text-pink-800',
];

$sizes = [
    'xs' => 'px-1.5 py-0.5 text-xs',
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-2.5 py-0.5 text-sm',
    'lg' => 'px-3 py-1 text-sm',
];

$roundeds = [
    'none' => 'rounded-none',
    'sm' => 'rounded-sm',
    'md' => 'rounded-md',
    'lg' => 'rounded-lg',
    'full' => 'rounded-full',
];

$dotColors = [
    'default' => 'bg-gray-400',
    'primary' => 'bg-blue-400',
    'secondary' => 'bg-gray-400',
    'success' => 'bg-green-400',
    'danger' => 'bg-red-400',
    'warning' => 'bg-yellow-400',
    'info' => 'bg-cyan-400',
    'purple' => 'bg-purple-400',
    'pink' => 'bg-pink-400',
];

$classes = 'inline-flex items-center font-medium ' .
    ($types[$type] ?? $types['default']) . ' ' .
    ($sizes[$size] ?? $sizes['md']) . ' ' .
    ($roundeds[$rounded] ?? $roundeds['full']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($dot)
        <span class="mr-1.5 h-1.5 w-1.5 rounded-full {{ $dotColors[$type] ?? $dotColors['default'] }}"></span>
    @endif
    {{ $slot }}
</span>
