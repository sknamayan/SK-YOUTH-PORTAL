@props([
    'title' => '',
    'buttonText' => 'Open',
])

<div x-data="{ showModal: false }" x-cloak>
    <!-- Sticky Bottom Action Bar: Fixed on Mobile, inline flow on Desktop -->
    <div
        class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-slate-900/95 px-4 pb-[max(1rem,env(safe-area-inset-bottom))] pt-4 backdrop-blur-md shadow-[0_-8px_30px_rgba(0,0,0,0.12)] border-t border-slate-100 dark:border-slate-800/80 md:relative md:bottom-auto md:left-auto md:right-auto md:z-auto md:bg-transparent md:p-0 md:shadow-none md:border-none md:mt-0"
    >
        <div class="mx-auto flex w-full max-w-7xl items-center justify-center md:justify-end">
            <button
                type="button"
                @click="showModal = true"
                class="flex w-full md:w-auto items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-[#1e40af] to-blue-600 px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-blue-500/20 hover:shadow-blue-500/35 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-500/10 cursor-pointer"
            >
                <!-- Add Calendar/Action Icon -->
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>{{ $buttonText }}</span>
            </button>
        </div>
    </div>

    <!-- Slide-up Bottom Sheet / Overlay -->
    <div
        x-show="showModal"
        class="fixed inset-0 z-50 flex items-end md:items-center justify-center overflow-hidden"
        style="display: none;"
    >
        <!-- Scrim / backdrop -->
        <div
            x-show="showModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showModal = false"
            class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"
            aria-hidden="true"
        ></div>

        <!-- Panel -->
        <div
            x-show="showModal"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-250 transform"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="relative w-full max-w-2xl bg-white dark:bg-slate-900 rounded-t-[2.25rem] md:rounded-[2.25rem] shadow-2xl z-50 max-h-[85vh] overflow-y-auto border border-slate-100 dark:border-slate-800/80 p-6 md:p-8 transform"
            style="display: none;"
            role="dialog"
            aria-modal="true"
            aria-label="{{ $title ?: 'Panel' }}"
        >
            <!-- Drag bar indicator for mobile layout -->
            <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-6 md:hidden"></div>

            <!-- Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-6">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white font-display uppercase tracking-tight truncate">{{ $title }}</h3>
                <button type="button" @click="showModal = false" class="p-2 rounded-xl text-slate-500 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition active:scale-95">
                    <span class="sr-only">Close</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content slot -->
            <div class="space-y-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>