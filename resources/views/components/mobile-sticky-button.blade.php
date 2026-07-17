@props([
    'href' => null,
    'type' => 'button',
    'form' => null,
    'disabled' => false,
    'hideOriginalSelector' => null,
    'bgColor' => 'bg-[#1e40af]',
    'textColor' => 'text-white'
])

@if($hideOriginalSelector)
    <style>
        @media (max-width: 767px) {
            {{ $hideOriginalSelector }} {
                display: none !important;
            }
        }
    </style>
@endif

<div 
    {{ $attributes->merge([
        'class' => 'fixed bottom-4 left-4 right-4 z-50 md:hidden p-3 rounded-2xl shadow-xl flex items-center justify-center bg-slate-900 border border-slate-800'
    ]) }}
>
    @if($href)
        <a 
            href="{{ $href }}"
            class="w-full text-center py-3 px-4 font-bold text-xs uppercase tracking-widest rounded-xl transition active:scale-95 shadow-md {{ $bgColor }} {{ $textColor }}"
            @if($disabled) aria-disabled="true" tabindex="-1" @endif
        >
            {{ $slot }}
        </a>
    @elseif($form)
        <button 
            type="{{ $type }}"
            form="{{ $form }}"
            class="w-full text-center py-3 px-4 font-bold text-xs uppercase tracking-widest rounded-xl transition active:scale-95 shadow-md {{ $bgColor }} {{ $textColor }}"
            {{ $disabled ? 'disabled' : '' }}
        >
            {{ $slot }}
        </button>
    @else
        <button 
            type="{{ $type }}"
            class="w-full text-center py-3 px-4 font-bold text-xs uppercase tracking-widest rounded-xl transition active:scale-95 shadow-md {{ $bgColor }} {{ $textColor }}"
            {{ $disabled ? 'disabled' : '' }}
        >
            {{ $slot }}
        </button>
    @endif
</div>
