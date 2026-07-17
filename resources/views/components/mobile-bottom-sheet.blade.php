@props([
    'closeLabel' => 'Close modal',
])

<div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-[1px]" aria-hidden="true">
    <div class="absolute inset-0" aria-hidden="true"></div>

    <div class="absolute bottom-0 left-0 right-0 rounded-t-2xl bg-slate-900 text-slate-100 shadow-2xl shadow-slate-950/60">
        <div class="px-4 pt-3 pb-3">
            <div class="mx-auto mb-3 h-1.5 w-12 rounded-full bg-slate-600/80"></div>

            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    @isset($subtitle)
                        <p class="text-[10px] font-black uppercase tracking-[0.24em] text-blue-300">
                            {{ $subtitle }}
                        </p>
                    @endisset

                    @isset($title)
                        <h2 class="mt-1 text-base font-black uppercase tracking-tight text-white">
                            {{ $title }}
                        </h2>
                    @endisset
                </div>

                <button
                    type="button"
                    aria-label="{{ $closeLabel }}"
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-slate-700 bg-slate-800/80 text-slate-300 transition hover:bg-slate-700 hover:text-white"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="px-4 pb-4">
            <div class="space-y-3">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
