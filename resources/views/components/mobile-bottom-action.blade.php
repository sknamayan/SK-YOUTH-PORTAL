@props([
    'href' => null,
    'type' => 'button',
    'form' => null,
    'disabled' => false,
])

{{--
    Mobile Bottom Action Button Component
    ==========================================================================
    A sticky, fixed-to-bottom action container visible only on mobile screens (md:hidden).
    Ensures primary calls-to-action remain thumb-accessible on small screens.

    This component keeps layout directives on the outer wrapper and forwards only the
    actual click/interaction attributes to the inner button for predictable mobile behavior.
--}}

<div
    {{ $attributes->except([
        'form',
        'type',
        'href',
        'disabled',
        '@click',
        'onclick',
        'x-on:click',
        'wire:click',
        'wire:target',
        'wire:loading.attr',
    'x-bind:disabled',
    ':disabled',
    'x-bind:class',
    ':class',
    'x-bind:aria-disabled',
    'aria-disabled',
])->merge([
    'class' => 'fixed bottom-0 left-0 right-0 z-40 md:hidden border-t border-white/10 bg-slate-900/95 px-3 pb-[max(0.85rem,env(safe-area-inset-bottom))] pt-3 backdrop-blur-xl shadow-[0_-10px_30px_rgba(0,0,0,0.25)]'
]) }}
>
<div class="mx-auto flex w-full max-w-5xl items-center justify-end pr-20 sm:pr-24">
        @if($href)
            <a
                href="{{ $href }}"
                {{ $attributes->only(['@click', 'onclick', 'x-on:click', 'wire:click', 'wire:target', 'x-bind:disabled', ':disabled', 'x-bind:class', ':class', 'x-bind:aria-disabled', 'aria-disabled'])->merge([
                    'class' => 'flex w-full max-w-[16rem] items-center justify-center rounded-2xl bg-gradient-to-r from-[#1e40af] to-blue-600 px-4 py-3 text-sm font-bold uppercase tracking-wider text-white shadow-lg shadow-blue-900/25 transition active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900',
                ]) }}
                @if($disabled) aria-disabled="true" tabindex="-1" @endif
            >
                {{ $slot }}
            </a>
        @elseif($form)
            <button
                type="{{ $type }}"
                form="{{ $form }}"
                {{ $attributes->only(['@click', 'onclick', 'x-on:click', 'wire:click', 'wire:target', 'x-bind:disabled', ':disabled', 'x-bind:class', ':class', 'x-bind:aria-disabled', 'aria-disabled'])->merge([
                    'class' => 'flex w-full max-w-[16rem] items-center justify-center rounded-2xl bg-gradient-to-r from-[#1e40af] to-blue-600 px-4 py-3 text-sm font-bold uppercase tracking-wider text-white shadow-lg shadow-blue-900/25 transition active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900',
                ]) }}
                @if($disabled) disabled @endif
            >
                {{ $slot }}
            </button>
        @else
            <button
                type="{{ $type }}"
                {{ $attributes->only(['@click', 'onclick', 'x-on:click', 'wire:click', 'wire:target', 'x-bind:disabled', ':disabled', 'x-bind:class', ':class', 'x-bind:aria-disabled', 'aria-disabled'])->merge([
                    'class' => 'flex w-full max-w-[16rem] items-center justify-center rounded-2xl bg-gradient-to-r from-[#1e40af] to-blue-600 px-4 py-3 text-sm font-bold uppercase tracking-wider text-white shadow-lg shadow-blue-900/25 transition active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900',
                ]) }}
                @if($disabled) disabled @endif
            >
                {{ $slot }}
            </button>
        @endif
    </div>
</div>
