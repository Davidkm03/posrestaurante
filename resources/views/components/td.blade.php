@props([
    'align' => 'left',
])

@php
$alignClasses = match ($align) {
    'left' => 'text-left',
    'center' => 'text-center',
    'right' => 'text-right',
    default => 'text-left',
};
@endphp

<td {{ $attributes->merge(['class' => "px-6 py-4 whitespace-nowrap text-sm text-gray-900 {$alignClasses}"]) }}>
    {{ $slot }}
</td>
