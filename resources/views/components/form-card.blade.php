@props(['title', 'subtitle', 'action', 'enctype' => null])

<div class="card max-w-2xl mx-auto overflow-hidden border border-slate-800 dark:border-slate-750 bg-slate-900 shadow-2xl">
    <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 -mx-6 -mt-6 md:-mx-8 md:-mt-8 px-6 md:px-8 py-5 text-white mb-6 border-b border-slate-800 flex justify-between items-start">
        <div class="flex-grow">
            <h2 class="text-sm font-black tracking-wider text-white font-display uppercase">{{ $title }}</h2>
            <p class="text-[11px] text-slate-400 mt-1 font-medium">{{ $subtitle }}</p>
        </div>
        <button type="button" @click="activeForm = null"
                class="text-slate-400 hover:text-white bg-slate-800/40 hover:bg-slate-800 p-2 rounded-xl transition focus:outline-none focus:ring-2 focus:ring-slate-700/50 -mt-1.5 -mr-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- Error Alert if rate limited -->
    @error('rate_limit')
        <div class="mb-5 p-3.5 bg-rose-50 border-l-4 border-rose-500 rounded-xl text-rose-800 text-xs font-semibold">
            {{ $message }}
        </div>
    @enderror

    <form method="POST" action="{{ $action }}" class="request-form space-y-5" @if($enctype) enctype="{{ $enctype }}" @endif>
        @csrf
        
        {{ $slot }}
    </form>
</div>
