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
        'class' => 'fixed bottom-0 left-0 right-0 z-50 md:hidden bg-slate-900/95 border-t border-slate-800/80 backdrop-blur-sm shadow-[0_-4px_12px_rgba(0,0,0,0.15)] pb-[max(12px,env(safe-area-inset-bottom))]'
    ]) }}
>
    <div class="px-4 py-3">
        @if($href)
            <a
                href="{{ $href }}"
                {{ $attributes->only(['@click', 'onclick', 'x-on:click', 'wire:click', 'wire:target'])->merge([
                    'class' => 'block w-full text-center rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 shadow-lg shadow-blue-950/25 transition active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900',
                ]) }}
                @if($disabled) aria-disabled="true" tabindex="-1" @endif
            >
                {{ $slot }}
            </a>
        @elseif($form)
            <button
                type="{{ $type }}"
                form="{{ $form }}"
                {{ $attributes->only(['@click', 'onclick', 'x-on:click', 'wire:click', 'wire:target'])->merge([
                    'class' => 'w-full rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 shadow-lg shadow-blue-950/25 transition active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900',
                ]) }}
                @if($disabled) disabled @endif
            >
                {{ $slot }}
            </button>
        @else
            <button
                type="{{ $type }}"
                {{ $attributes->only(['@click', 'onclick', 'x-on:click', 'wire:click', 'wire:target'])->merge([
                    'class' => 'w-full rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 shadow-lg shadow-blue-950/25 transition active:scale-[0.99] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900',
                ]) }}
                @if($disabled) disabled @endif
            >
                {{ $slot }}
            </button>
        @endif
    </div>
</div>
