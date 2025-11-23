@props([
    'name' => '',
    'label' => null,
    'required' => false,
    'disabled' => false,
    'placeholder' => 'Seleccionar...',
    'options' => [],
    'selected' => null,
])

<div {{ $attributes->only('class')->merge(['class' => 'w-full']) }}>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        @if($required) required @endif
        @if($disabled) disabled @endif
        {{ $attributes->except('class')->merge([
            'class' => 'block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm disabled:bg-gray-50 disabled:text-gray-500' .
                ($errors->has($name) ? ' border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500' : '')
        ]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @forelse($options as $value => $optionLabel)
            <option value="{{ $value }}" @selected(old($name, $selected) == $value)>
                {{ $optionLabel }}
            </option>
        @empty
            {{ $slot }}
        @endforelse
    </select>

    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
