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
])->merge([
    'class' => 'fixed bottom-6 left-1/2 -translate-x-1/2 w-[90%] max-w-lg z-45 md:hidden flex items-center justify-center bg-transparent border-none shadow-none pointer-events-none p-0'
]) }}
>
    <div class="flex w-full items-center justify-center">
        @if($href)
            <a
                href="{{ $href }}"
                {{ $attributes->only(['@click', 'onclick', 'x-on:click', 'wire:click', 'wire:target', 'x-bind:disabled', ':disabled', 'x-bind:class', ':class', 'x-bind:aria-disabled', 'aria-disabled'])->merge([
                    'class' => 'flex w-full items-center justify-center rounded-2xl border border-blue-600 dark:border-blue-500 bg-white/95 dark:bg-slate-900/95 text-blue-600 dark:text-sky-400 backdrop-blur-md px-4 py-3.5 text-xs font-black uppercase tracking-widest shadow-lg transition active:scale-[0.98] focus:outline-none pointer-events-auto',
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
                    'class' => 'flex w-full items-center justify-center rounded-2xl border border-blue-600 dark:border-blue-500 bg-white/95 dark:bg-slate-900/95 text-blue-600 dark:text-sky-400 backdrop-blur-md px-4 py-3.5 text-xs font-black uppercase tracking-widest shadow-lg transition active:scale-[0.98] focus:outline-none pointer-events-auto',
                ]) }}
                @if($disabled) disabled @endif
            >
                {{ $slot }}
            </button>
        @else
            <button
                type="{{ $type }}"
                {{ $attributes->only(['@click', 'onclick', 'x-on:click', 'wire:click', 'wire:target', 'x-bind:disabled', ':disabled', 'x-bind:class', ':class', 'x-bind:aria-disabled', 'aria-disabled'])->merge([
                    'class' => 'flex w-full items-center justify-center rounded-2xl border border-blue-600 dark:border-blue-500 bg-white/95 dark:bg-slate-900/95 text-blue-600 dark:text-sky-400 backdrop-blur-md px-4 py-3.5 text-xs font-black uppercase tracking-widest shadow-lg transition active:scale-[0.98] focus:outline-none pointer-events-auto',
                ]) }}
                @if($disabled) disabled @endif
            >
                {{ $slot }}
            </button>
        @endif
    </div>
</div>
