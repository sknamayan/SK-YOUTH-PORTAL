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
    'class' => 'w-full md:hidden border-t border-slate-100 bg-white px-3 py-4 mt-6'
]) }}
>
<div class="mx-auto flex w-full max-w-5xl items-center justify-center sm:justify-end">
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
