@props(['label', 'name', 'type' => 'text', 'required' => false, 'value' => '', 'placeholder' => '', 'min' => '', 'max' => ''])

@php
    $errorKey = str_replace(['[', ']'], ['.', ''], $name);
    $errorKey = rtrim($errorKey, '.');
@endphp

<div>
    <label for="{{ $name }}" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
        {{ $label }}
        @if($required)
            <span class="text-rose-500 font-extrabold" title="Required Field">*</span>
        @endif
    </label>

    @if($type === 'textarea')
        <textarea 
            id="{{ $name }}" 
            name="{{ $name }}" 
            rows="3" 
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            class="field focus:ring-4 focus:ring-blue-600/10">{{ old($errorKey, $value) }}</textarea>
    @else
        <input 
            type="{{ $type }}" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            value="{{ old($errorKey, $value) }}"
            placeholder="{{ $placeholder }}"
            min="{{ $min }}"
            max="{{ $max }}"
            {{ $required ? 'required' : '' }}
            class="field focus:ring-4 focus:ring-blue-600/10"
        >
    @endif

    @error($errorKey)
        <span class="text-rose-600 text-xs font-semibold mt-1 block">{{ $message }}</span>
    @enderror
</div>
