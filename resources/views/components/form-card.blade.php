@props(['title', 'subtitle', 'action', 'enctype' => null])

<div class="card max-w-2xl mx-auto overflow-hidden border border-slate-800 dark:border-slate-750 bg-slate-900 shadow-2xl">
    <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 -mx-6 -mt-6 md:-mx-8 md:-mt-8 px-6 md:px-8 py-5 text-white mb-6 border-b border-slate-800">
        <h2 class="text-sm font-black tracking-wider text-white font-display uppercase">{{ $title }}</h2>
        <p class="text-[11px] text-slate-400 mt-1 font-medium">{{ $subtitle }}</p>
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
