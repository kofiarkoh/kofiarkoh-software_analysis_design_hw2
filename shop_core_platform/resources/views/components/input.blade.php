@props([
    'type' => 'text',
    'id' => null,
    'placeholder' => '',
    'required' => false,
    'value' => '',
    'name' => null,
    'label' => null,
])

@php
    $inputName = $name ?? $id;
    $inputId = $id ?? $name;
@endphp

<div class="w-full">
    @if ($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }} @if($required)<span class="text-red">*</span>@endif
        </label>
    @endif

    <input
        {{ $attributes->merge([
            'class' => 'border-line px-4 pt-3 pb-3 w-full rounded-lg ' . ($errors->has($inputName) ? 'border-red' : '')
        ]) }}
        type="{{ $type }}"
        id="{{ $inputId }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        @if($required) required @endif
        value="{{ old($name, $value) }}"
    />

    @error($inputName)
    <p class="text-red text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
