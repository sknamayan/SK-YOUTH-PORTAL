@props(['label', 'name', 'required' => false, 'options' => [], 'selected' => ''])

<div>
    <label for="{{ $name }}" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">
        {{ $label }}
        @if($required)
            <span class="text-rose-500 font-extrabold" title="Required Field">*</span>
        @endif
    </label>

    <select 
        id="{{ $name }}" 
        name="{{ $name }}" 
        {{ $required ? 'required' : '' }}
        class="field focus:ring-4 focus:ring-blue-600/10"
    >
        <option value="" disabled {{ old($name, $selected) === '' ? 'selected' : '' }}>Select option...</option>
        @foreach($options as $val => $text)
            @php
                $actualValue = is_numeric($val) && $val == $text ? $text : $val;
            @endphp
            <option value="{{ $actualValue }}" {{ old($name, $selected) == $actualValue ? 'selected' : '' }}>
                {{ $text }}
            </option>
        @endforeach
    </select>

    @error($name)
        <span class="text-rose-600 text-xs font-semibold mt-1 block">{{ $message }}</span>
    @enderror
</div>
