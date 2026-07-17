@props([
    'href' => null,
    'type' => 'button',
    'form' => null,
])

<div class="md:hidden fixed inset-x-0 bottom-0 z-50 border-t border-slate-800/80 bg-slate-900/95 backdrop-blur-sm">
    <div class="px-4 py-3">
        @if($href)
            <a
                href="{{ $href }}"
                {{ $attributes->merge([
                    'class' => 'block w-full text-center rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 shadow-lg shadow-blue-950/25 transition active:scale-[0.99]',
                ]) }}
            >
                {{ $slot }}
            </a>
        @elseif($form)
            <button
                type="{{ $type }}"
                form="{{ $form }}"
                {{ $attributes->merge([
                    'class' => 'w-full rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 shadow-lg shadow-blue-950/25 transition active:scale-[0.99]',
                ]) }}
            >
                {{ $slot }}
            </button>
        @else
            <button
                type="{{ $type }}"
                {{ $attributes->merge([
                    'class' => 'w-full rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 shadow-lg shadow-blue-950/25 transition active:scale-[0.99]',
                ]) }}
            >
                {{ $slot }}
            </button>
        @endif
    </div>
</div>
