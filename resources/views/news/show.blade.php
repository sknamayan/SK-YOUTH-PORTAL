@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-8 py-10 md:py-16 flex-1 font-sans">
    
    <!-- Breadcrumbs -->
    <div class="flex items-center space-x-2 text-[10px] sm:text-xs font-bold uppercase tracking-wider text-slate-400 mb-6 pb-4 border-b border-slate-100">
        <a href="/" class="hover:text-[#1e40af] transition duration-150">Home</a>
        <span class="text-slate-350 select-none">/</span>
        <span class="text-slate-800">News Articles</span>
    </div>

    <!-- Article Header -->
    <header class="space-y-4 mb-8">
        <div class="flex flex-wrap items-center gap-3">
            <span class="bg-blue-50 border border-blue-100 text-[#1e40af] text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full">{{ $article->category }}</span>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $article->read_time }} Min Read</span>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">•</span>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Published: {{ $article->published_at ? $article->published_at->format('M d, Y') : $article->created_at->format('M d, Y') }}</span>
        </div>
        <h1 class="text-2xl sm:text-4xl font-black text-slate-900 leading-tight font-display tracking-tight uppercase">{{ $article->title }}</h1>
        <p class="text-sm text-slate-500 font-medium leading-relaxed italic border-l-2 border-slate-200 pl-4">{{ $article->excerpt }}</p>
    </header>

    <!-- Cover Image -->
    @if($article->image_path)
        <div class="relative w-full h-[240px] sm:h-[400px] rounded-3xl overflow-hidden shadow-sm border border-slate-100 mb-10">
            @if(Str::startsWith($article->image_path, 'http'))
                <img src="{{ $article->image_path }}" class="w-full h-full object-cover" alt="{{ $article->title }}">
            @else
                <img src="{{ asset('storage/' . $article->image_path) }}" class="w-full h-full object-cover" alt="{{ $article->title }}">
            @endif
        </div>
    @endif

    <!-- Content Body -->
    <article class="prose prose-slate max-w-none text-slate-700 text-sm leading-relaxed space-y-6 font-medium">
        {!! nl2br(e($article->content)) !!}
    </article>

    <!-- Back to top / share area -->
    <div class="mt-12 pt-6 border-t border-slate-100 flex items-center justify-between">
        <a href="/" class="btn-outline text-xs border border-slate-250 py-2.5 px-6 rounded-xl text-slate-600 hover:bg-slate-50 font-bold transition">
            &larr; Back to Homepage
        </a>
    </div>

</div>
@endsection
