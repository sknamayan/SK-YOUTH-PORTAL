@extends('layouts.app')

@section('content')
<div class="flex-1 flex flex-col min-h-0 bg-slate-50 dark:bg-slate-950 font-sans {{ $post->fileUrl() ? 'pb-[max(5.5rem,env(safe-area-inset-bottom))]' : 'pb-[max(1.5rem,env(safe-area-inset-bottom))]' }} md:pb-10">

    {{-- Sticky mobile toolbar --}}
    <div class="sticky top-16 z-20 md:hidden bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800">
        <div class="flex items-center h-12 px-3">
            <a href="{{ route('transparency.index') }}" class="inline-flex items-center justify-center min-w-11 min-h-11 rounded-xl text-slate-600 dark:text-slate-300 active:scale-95 transition-all" aria-label="Back to transparency board">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <span class="flex-1 text-center text-[10px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 truncate px-2">Document</span>
            <div class="w-11"></div>
        </div>
    </div>

    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-[#1e3a8a] text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-12">
            <nav aria-label="Breadcrumb" class="hidden md:flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-6 min-w-0">
                <a href="{{ route('landing') }}" class="hover:text-white shrink-0">Home</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('transparency.index') }}" class="hover:text-white shrink-0">Transparency</a>
                <span aria-hidden="true">/</span>
                <span class="truncate" aria-current="page">{{ Str::limit($post->title, 40) }}</span>
            </nav>
            <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-[9px] font-black uppercase tracking-widest mb-3">{{ $post->categoryLabel() }}</span>
            <h1 class="text-lg sm:text-xl md:text-3xl font-black font-display uppercase tracking-tight leading-snug">{{ $post->title }}</h1>
            <p class="text-xs md:text-sm text-slate-400 mt-3 font-semibold">Published {{ $post->published_at->format('F d, Y') }}</p>
        </div>
    </section>

    <article class="max-w-4xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-5 md:py-12 space-y-5 md:space-y-8">
        @if($post->imageUrl())
            <div class="rounded-2xl md:rounded-3xl overflow-hidden border border-slate-200/80 dark:border-slate-800 shadow-sm aspect-video bg-slate-100 dark:bg-slate-800">
                <img src="{{ $post->imageUrl() }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
            </div>
        @endif

        <div class="rounded-2xl md:rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm p-5 md:p-6 space-y-4">
            <p class="text-sm text-slate-700 dark:text-slate-200 leading-relaxed font-medium">{{ $post->excerpt }}</p>
            @if($post->content)
                <div class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line border-t border-slate-100 dark:border-slate-800 pt-4">{{ $post->content }}</div>
            @endif
        </div>

        @if($post->fileUrl())
            <div class="hidden md:flex rounded-3xl p-6 items-center justify-between gap-4 bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900">
                <div class="min-w-0">
                    <span class="text-[10px] font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-widest">Attached Document</span>
                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200 mt-1">Download the official file for this disclosure.</p>
                </div>
                <a href="{{ $post->fileUrl() }}" target="_blank" rel="noopener" download class="inline-flex items-center gap-2 min-h-11 px-6 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase rounded-xl active:scale-95 transition-all shrink-0">
                    Download File
                </a>
            </div>
        @endif

        @if($related->isNotEmpty())
            <div class="space-y-3">
                <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest font-display px-1">Related Documents</h2>
                <ul class="space-y-2" role="list">
                    @foreach($related as $item)
                        <li>
                            <a href="{{ route('transparency.show', $item->slug) }}" class="flex items-center gap-3 min-h-[3.25rem] p-3 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm active:scale-[0.98] transition-all min-w-0">
                                <div class="w-10 h-10 shrink-0 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-black text-slate-900 dark:text-slate-100 uppercase truncate">{{ $item->title }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $item->published_at->format('M d, Y') }}</p>
                                </div>
                                <svg class="w-4 h-4 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <a href="{{ route('transparency.index') }}" class="hidden md:inline-flex btn-outline text-xs min-h-11 items-center active:scale-95">← Back to Transparency Board</a>
    </article>

    {{-- Mobile sticky download bar --}}
    @if($post->fileUrl())
        <div class="fixed bottom-0 inset-x-0 z-30 md:hidden px-4 pb-[max(0.75rem,env(safe-area-inset-bottom))] pt-2 bg-gradient-to-t from-slate-50 via-slate-50/95 to-transparent dark:from-slate-950 dark:via-slate-950/95 pointer-events-none">
            <a href="{{ $post->fileUrl() }}" target="_blank" rel="noopener" download
               class="pointer-events-auto flex items-center justify-center gap-2 w-full min-h-[3.25rem] bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-emerald-900/20 active:scale-[0.98] transition-all duration-200">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download Document
            </a>
        </div>
    @endif
</div>

@include('partials.governance-mobile-js')
@endsection
