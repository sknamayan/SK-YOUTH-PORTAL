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
    @elseif($type === 'password')
        <div x-data="{ showPassword: false }" class="relative">
            <input 
                :type="showPassword ? 'text' : 'password'" 
                id="{{ $name }}" 
                name="{{ $name }}" 
                value="{{ old($errorKey, $value) }}"
                placeholder="{{ $placeholder }}"
                {{ $required ? 'required' : '' }}
                class="field focus:ring-4 focus:ring-blue-600/10 pr-10"
            >
            <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-655 active:scale-95 transition focus:outline-none z-10">
                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
                </svg>
            </button>
        </div>
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
