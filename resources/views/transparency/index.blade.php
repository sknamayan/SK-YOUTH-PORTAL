@extends('layouts.app')

@section('content')
<div class="flex-1 flex flex-col min-h-0 bg-slate-50 dark:bg-slate-950 font-sans">

    <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-[#1e3a8a] text-white shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 pt-[max(1.5rem,env(safe-area-inset-top))] pb-8 md:py-16">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-5 min-w-0">
                <a href="{{ route('landing') }}" class="hover:text-white active:scale-95 shrink-0">Home</a>
                <span aria-hidden="true" class="shrink-0">/</span>
                <span class="text-white truncate" aria-current="page">Community Board</span>
            </nav>
            <div class="max-w-2xl space-y-2.5">
                <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-[9px] font-black uppercase tracking-widest">Open Governance</span>
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-black font-display uppercase tracking-tight leading-tight">Community Board</h1>
                <p class="text-sm text-slate-300 leading-relaxed">Public documents, budget reports, resolutions, and official disclosures from SK Namayan.</p>
            </div>
            <a href="{{ route('officials.index') }}" class="inline-flex items-center min-h-11 mt-6 px-5 bg-white/10 hover:bg-white/20 border border-white/20 font-bold text-xs uppercase tracking-wider rounded-2xl active:scale-95 transition-all">
                Meet SK Officials →
            </a>
        </div>
    </section>

    {{-- Sticky category filter --}}
    <div class="sticky top-16 z-20 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 py-3">
            <div class="flex gap-2 overflow-x-auto snap-x snap-mandatory overscroll-x-contain pb-0.5 -mx-1 px-1 scrollbar-hide" role="tablist" aria-label="Filter by category">
                <a href="{{ route('transparency.index') }}"
                   role="tab"
                   aria-selected="{{ !$category ? 'true' : 'false' }}"
                   class="snap-start shrink-0 inline-flex items-center min-h-11 px-4 rounded-xl text-[10px] font-black uppercase tracking-wider transition-all duration-200 active:scale-95 {{ !$category ? 'bg-[#1e40af] text-white shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                    All
                </a>
                @foreach($categories as $key => $label)
                    <a href="{{ route('transparency.index', ['category' => $key]) }}"
                       role="tab"
                       aria-selected="{{ $category === $key ? 'true' : 'false' }}"
                       class="snap-start shrink-0 inline-flex items-center min-h-11 px-4 rounded-xl text-[10px] font-black uppercase tracking-wider whitespace-nowrap transition-all duration-200 active:scale-95 {{ $category === $key ? 'bg-[#1e40af] text-white shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <section class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-8 py-5 md:py-12 pb-[max(1.5rem,env(safe-area-inset-bottom))]">
        @if($posts->isEmpty())
            <div class="flex flex-col items-center text-center py-20 px-6 rounded-3xl bg-white dark:bg-slate-900 border border-dashed border-slate-200 dark:border-slate-700">
                <h2 class="text-sm font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider">No Documents Found</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 max-w-xs leading-relaxed">
                    @if($category)
                        No posts in this category yet. Try another filter or check back soon.
                    @else
                        Transparency documents will appear here once published by SK Namayan.
                    @endif
                </p>
                @if($category)
                    <a href="{{ route('transparency.index') }}" class="mt-6 min-h-11 inline-flex items-center px-5 btn-outline text-xs active:scale-95">View All Documents</a>
                @endif
            </div>
        @else
            {{-- Mobile: edge-to-edge list --}}
            <ul class="md:hidden space-y-3 -mx-4 px-4" role="list">
                @foreach($posts as $post)
                    <li>
                        <article class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden active:scale-[0.99] transition-all duration-200">
                            <a href="{{ route('transparency.show', $post->slug) }}" class="block p-4 space-y-2 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[9px] font-black uppercase text-[#1e40af] dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50 px-2 py-0.5 rounded-md truncate max-w-[60%]">{{ $post->categoryLabel() }}</span>
                                    <time datetime="{{ $post->published_at->toDateString() }}" class="text-[9px] font-bold text-slate-400 shrink-0">{{ $post->published_at->format('M d, Y') }}</time>
                                </div>
                                <h2 class="text-sm font-black text-slate-900 dark:text-slate-100 font-display uppercase leading-snug line-clamp-2">{{ $post->title }}</h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">{{ $post->excerpt }}</p>
                            </a>
                            <div class="flex border-t border-slate-100 dark:border-slate-800 divide-x divide-slate-100 dark:divide-slate-800">
                                <a href="{{ route('transparency.show', $post->slug) }}" class="flex-1 min-h-11 flex items-center justify-center text-[10px] font-bold uppercase text-[#1e40af] dark:text-blue-400 active:bg-slate-50 dark:active:bg-slate-800 transition-colors">Read</a>
                                @if($post->fileUrl())
                                    <a href="{{ $post->fileUrl() }}" target="_blank" rel="noopener" download class="flex-1 min-h-11 flex items-center justify-center text-[10px] font-bold uppercase text-emerald-600 active:bg-slate-50 dark:active:bg-slate-800 transition-colors">Download</a>
                                @endif
                            </div>
                        </article>
                    </li>
                @endforeach
            </ul>

            {{-- Desktop: card grid --}}
            <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($posts as $post)
                    <article class="rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col hover:shadow-md transition-all group">
                        @if($post->imageUrl())
                            <div class="aspect-video overflow-hidden bg-slate-50 dark:bg-slate-800">
                                <img src="{{ $post->imageUrl() }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy">
                            </div>
                        @else
                            <div class="aspect-video bg-gradient-to-br from-slate-50 to-blue-50 dark:from-slate-800 dark:to-slate-900 flex items-center justify-center">
                                <svg class="w-12 h-12 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                        @endif
                        <div class="p-5 flex-1 flex flex-col gap-2 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[9px] font-black uppercase text-[#1e40af] dark:text-blue-400 bg-blue-50 dark:bg-blue-950/50 px-2 py-0.5 rounded-md truncate">{{ $post->categoryLabel() }}</span>
                                <time class="text-[9px] font-bold text-slate-400 shrink-0">{{ $post->published_at->format('M d, Y') }}</time>
                            </div>
                            <h2 class="text-sm font-black text-slate-900 dark:text-slate-100 font-display uppercase line-clamp-2">
                                <a href="{{ route('transparency.show', $post->slug) }}" class="hover:text-[#1e40af] dark:hover:text-blue-400 transition">{{ $post->title }}</a>
                            </h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-3 flex-1">{{ $post->excerpt }}</p>
                            <div class="flex gap-3 pt-2">
                                <a href="{{ route('transparency.show', $post->slug) }}" class="text-[10px] font-bold uppercase text-[#1e40af]">Read More →</a>
                                @if($post->fileUrl())
                                    <a href="{{ $post->fileUrl() }}" target="_blank" rel="noopener" download class="text-[10px] font-bold uppercase text-emerald-600">Download</a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8 md:mt-10">{{ $posts->links() }}</div>
        @endif
    </section>
</div>

@include('partials.governance-mobile-js')
@endsection
