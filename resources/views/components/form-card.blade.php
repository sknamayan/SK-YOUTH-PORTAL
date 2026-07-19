@props(['title', 'subtitle', 'action', 'enctype' => null])

<div class="card max-w-2xl mx-auto overflow-hidden">
    <div class="bg-gradient-to-r from-blue-700 to-blue-900 -mx-6 -mt-6 md:-mx-8 md:-mt-8 px-6 md:px-8 py-5 text-white mb-6">
        <h2 class="text-lg font-bold tracking-tight text-white font-display uppercase">{{ $title }}</h2>
        <p class="text-xs text-blue-200 mt-1">{{ $subtitle }}</p>
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
