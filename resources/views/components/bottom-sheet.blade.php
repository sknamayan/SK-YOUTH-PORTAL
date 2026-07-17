@props([
    'title' => '',
    'buttonText' => 'Open',
])

<div x-data="{ showModal: false }" x-cloak>
    <!-- Sticky Bottom Action Bar (mobile-only) -->
    <div
        class="fixed bottom-0 left-0 right-0 z-40 md:hidden border-t border-white/10 bg-slate-900/95 px-3 pb-[max(0.85rem,env(safe-area-inset-bottom))] pt-3 backdrop-blur-xl shadow-[0_-10px_30px_rgba(0,0,0,0.25)]"
    >
        <div class="mx-auto flex w-full max-w-5xl items-center justify-end pr-24 sm:pr-24">
            <button
                type="button"
                @click="showModal = true"
                class="flex w-full max-w-[16rem] items-center justify-center rounded-2xl bg-gradient-to-r from-[#1e40af] to-blue-600 px-4 py-3 text-sm font-bold uppercase tracking-wider text-white shadow-lg transition active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                {{ $buttonText }}
            </button>
        </div>
    </div>

    <!-- Slide-up Bottom Sheet / Overlay -->
    <div
        x-show="showModal"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-end justify-center"
        style="display: none;"
    >
        <!-- Scrim / backdrop -->
        <div
            @click="showModal = false"
            class="absolute inset-0 bg-black/40 backdrop-blur-sm"
            aria-hidden="true"
        ></div>

        <!-- Panel -->
        <div
            x-show="showModal"
            x-transition:enter="transition-transform duration-300"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition-transform duration-250"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="relative w-full max-w-3xl mx-4 mb-4 rounded-t-2xl bg-white dark:bg-slate-900 shadow-2xl transform"
            style="display: none;"
            role="dialog"
            aria-modal="true"
            aria-label="{{ $title ?: 'Panel' }}"
        >
            <!-- Header -->
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-800 rounded-t-2xl">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $title }}</h3>
                <button type="button" @click="showModal = false" class="p-2 rounded-md text-slate-500 hover:text-slate-700 dark:hover:text-white">
                    <span class="sr-only">Close</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Content slot (form / custom content) -->
            <div class="p-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>