@props([
    'name',
    'value' => '',
    'min'   => '',
    'max'   => '',
    'required' => false,
    'placeholder' => '',
    // Tailwind or custom classes for styling
    'class' => 'border rounded p-2 w-full bg-white/70 backdrop-blur-sm',
    // Enable time picker (optional)
    'enableTime' => false,
])

<div x-data
     x-init="flatpickr($refs.input, {
        dateFormat: 'Y-m-d',
        @if($enableTime) enableTime: true, @endif
        @if($min) minDate: '{{ $min }}', @endif
        @if($max) maxDate: '{{ $max }}', @endif
     })">
    <input
        x-ref="input"
        type="text"
        name="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        class="{{ $class }}"
        {{ $attributes }}
        autocomplete="off">
</div>
