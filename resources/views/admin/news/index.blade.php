@extends('layouts.app')

@section('content')
<div
    x-data="newsAdmin({
        openOnLoad: {{ $errors->any() ? 'true' : 'false' }}
    })"
    x-init="init()"
    class="flex-1 flex flex-col md:flex-row bg-[#f8fafc] dark:bg-slate-950 min-h-0"
>

    @include('layouts.dashboard-sidebar')

    {{-- Mobile sidebar backdrop --}}
    <div
        x-show="mobileSidebar"
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="mobileSidebar = false"
        class="fixed inset-0 bg-slate-900/50 dark:bg-black/60 z-20 md:hidden"
        aria-hidden="true"
        x-cloak
    ></div>

    {{-- Main content shell --}}
    <div class="flex-1 flex flex-col min-w-0 min-h-0 md:min-h-[calc(100dvh-4rem)]">

        {{-- Sticky mobile app bar --}}

        {{-- Scrollable page body --}}
        <div class="flex-1 overflow-y-auto overscroll-y-contain p-4 md:p-8 space-y-4 md:space-y-6 pb-24 md:pb-8">

            {{-- Breadcrumbs --}}
            <nav aria-label="Breadcrumb" class="flex items-center pb-3 md:pb-4 border-b border-slate-100 dark:border-slate-800">
                <ol class="flex items-center gap-2 text-[10px] md:text-xs font-semibold uppercase tracking-wider min-w-0">
                    <li class="shrink-0">
                        <a href="{{ route('dashboard.index') }}" class="text-slate-400 dark:text-slate-500 hover:text-[#1e40af] dark:hover:text-blue-400 transition duration-150">Dashboard</a>
                    </li>
                    <li class="text-slate-300 dark:text-slate-600 shrink-0" aria-hidden="true">/</li>
                    <li class="text-slate-800 dark:text-slate-200 truncate" aria-current="page">News Articles</li>
                </ol>
            </nav>

            {{-- Page header --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="space-y-1 min-w-0">
                    <span class="text-[10px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">News & Updates</span>
                    <h1 class="text-xl md:text-2xl font-black tracking-tight text-slate-800 dark:text-slate-100 font-display uppercase">Manage News Articles</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Upload, edit, and categorize announcements or articles displayed on the homepage.</p>
                </div>
                @if(Auth::user()->isAdmin())
                    <button
                        type="button"
                        @click="openAddArticleModal = true"
                        class="hidden sm:inline-flex btn-primary text-xs shrink-0 items-center gap-2 min-h-11"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Create News Article</span>
                    </button>
                @endif
            </div>

            {{-- Articles Card --}}
            <div class="card p-0 overflow-hidden bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl md:rounded-3xl shadow-sm">

                @if($articles->isEmpty())
                    <div class="text-center py-12 md:py-16 px-4 space-y-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">No Articles Published</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">Click "Create News Article" above to publish your first update on the citizen website.</p>
                        </div>
                    </div>
                @else

                    {{-- Mobile view --}}
                    <ul class="md:hidden divide-y divide-slate-100 dark:divide-slate-800" role="list" aria-label="News Articles">
                        @foreach($articles as $article)
                            <li class="p-4 space-y-3">
                                <div class="flex gap-3 min-w-0">
                                    <div class="w-20 h-12 shrink-0 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 overflow-hidden flex items-center justify-center">
                                        @if($article->image_path)
                                            <img src="{{ asset('storage/' . $article->image_path) }}" class="w-full h-full object-cover" alt="Thumbnail">
                                        @else
                                            <span class="text-slate-400">📷</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0 space-y-1">
                                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-snug truncate">
                                            <a href="{{ route('news.show', $article->slug) }}" target="_blank" class="hover:text-[#1e40af] dark:hover:text-blue-400 transition">{{ $article->title }}</a>
                                        </h3>
                                        <div class="flex items-center gap-2 text-[10px] text-slate-400 dark:text-slate-500 font-mono">
                                            <span class="capitalize font-semibold text-slate-500 dark:text-slate-400">{{ $article->category }}</span>
                                            <span>·</span>
                                            <span>{{ $article->read_time }} min read</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-50 dark:border-slate-800">
                                    <div class="flex items-center gap-1.5">
                                        @if($article->is_featured)
                                            <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950/40 text-emerald-855 dark:text-emerald-300 rounded text-[9px] font-black uppercase tracking-wider">Featured</span>
                                        @endif
                                        @if($article->is_trending)
                                            <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-950/40 text-blue-855 dark:text-blue-300 rounded text-[9px] font-black uppercase tracking-wider">Trending</span>
                                        @endif
                                    </div>

                                    @if(Auth::user()->isAdmin())
                                        <div class="flex items-center gap-2">
                                            <button
                                                type="button"
                                                @click="editArticle({
                                                    id: {{ $article->id }},
                                                    title: '{{ addslashes($article->title) }}',
                                                    category: '{{ addslashes($article->category) }}',
                                                    read_time: {{ $article->read_time }},
                                                    excerpt: '{{ addslashes(str_replace(["\r", "\n"], ' ', $article->excerpt)) }}',
                                                    content: '{{ addslashes(str_replace(["\r", "\n"], ' ', $article->content)) }}',
                                                    is_featured: {{ $article->is_featured ? 1 : 0 }},
                                                    is_trending: {{ $article->is_trending ? 1 : 0 }},
                                                    image_url: '{{ $article->image_path ? asset('storage/' . $article->image_path) : '' }}'
                                                })"
                                                class="inline-flex items-center min-h-9 px-3 py-1 bg-blue-50 dark:bg-blue-950/40 text-[#1e40af] dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-950/70 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95 border border-transparent"
                                            >
                                                Edit
                                            </button>
                                            <x-alert-dialog>
                                                <x-slot:trigger>
                                                    <button
                                                        type="button"
                                                        class="inline-flex items-center min-h-9 px-3 py-1 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 hover:bg-rose-100 dark:hover:bg-rose-950/70 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95 border border-transparent"
                                                    >
                                                        Delete
                                                    </button>
                                                </x-slot:trigger>

                                                <x-slot:icon>
                                                    <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                    </svg>
                                                </x-slot:icon>

                                                <x-slot:title>Delete News Article</x-slot:title>

                                                <x-slot:description>
                                                    Are you sure you want to permanently delete "{{ $article->title }}"? This will remove it from the homepage feed. This action cannot be undone.
                                                </x-slot:description>

                                                <x-slot:footer>
                                                    <button type="button" @click="open = false" class="btn-outline text-xs py-2 px-4 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                                        Cancel
                                                    </button>
                                                    <form method="POST" action="{{ route('admin.news.destroy', $article->id) }}" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2 px-4 rounded-xl text-xs transition active:scale-95 shadow-sm border border-transparent">
                                                            Confirm Delete
                                                        </button>
                                                    </form>
                                                </x-slot:footer>
                                            </x-alert-dialog>
                                        </div>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Desktop view --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-700 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider font-display">
                                    <th class="p-4 pl-6">Preview</th>
                                    <th class="p-4">Title</th>
                                    <th class="p-4">Category</th>
                                    <th class="p-4">Read Time</th>
                                    <th class="p-4 text-center">Status Badges</th>
                                    @if(Auth::user()->isAdmin())
                                        <th class="p-4 pr-6 text-right">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-600 dark:text-slate-350">
                                @foreach($articles as $article)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition duration-150">
                                        <td class="p-4 pl-6">
                                            <div class="w-20 h-12 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 overflow-hidden flex items-center justify-center shrink-0">
                                                @if($article->image_path)
                                                    <img src="{{ asset('storage/' . $article->image_path) }}" class="w-full h-full object-cover" alt="Article thumbnail">
                                                @else
                                                    <span class="text-slate-400">📷</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="p-4 font-bold text-slate-800 dark:text-slate-100 max-w-xs truncate" title="{{ $article->title }}">
                                            <a href="{{ route('news.show', $article->slug) }}" target="_blank" class="hover:text-[#1e40af] dark:hover:text-blue-400 transition">{{ $article->title }}</a>
                                        </td>
                                        <td class="p-4 font-semibold text-slate-700 dark:text-slate-400 capitalize">
                                            {{ $article->category }}
                                        </td>
                                        <td class="p-4 font-medium text-slate-500 dark:text-slate-500">
                                            {{ $article->read_time }} Min
                                        </td>
                                        <td class="p-4 text-center space-x-1 whitespace-nowrap">
                                            @if($article->is_featured)
                                                <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 rounded text-[9px] font-black uppercase tracking-wider">Featured</span>
                                            @endif
                                            @if($article->is_trending)
                                                <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-950/40 text-blue-800 dark:text-blue-300 rounded text-[9px] font-black uppercase tracking-wider">Trending</span>
                                            @endif
                                            @if(!$article->is_featured && !$article->is_trending)
                                                <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 rounded text-[9px] font-black uppercase tracking-wider">Standard</span>
                                            @endif
                                        </td>
                                        @if(Auth::user()->isAdmin())
                                            <td class="p-4 pr-6 text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button
                                                        type="button"
                                                        @click="editArticle({
                                                            id: {{ $article->id }},
                                                            title: '{{ addslashes($article->title) }}',
                                                            category: '{{ addslashes($article->category) }}',
                                                            read_time: {{ $article->read_time }},
                                                            excerpt: '{{ addslashes(str_replace(["\r", "\n"], ' ', $article->excerpt)) }}',
                                                            content: '{{ addslashes(str_replace(["\r", "\n"], ' ', $article->content)) }}',
                                                            is_featured: {{ $article->is_featured ? 1 : 0 }},
                                                            is_trending: {{ $article->is_trending ? 1 : 0 }},
                                                            image_url: '{{ $article->image_path ? asset('storage/' . $article->image_path) : '' }}'
                                                        })"
                                                        class="inline-flex items-center min-h-9 px-2.5 py-1.5 bg-blue-50 dark:bg-blue-950/40 text-[#1e40af] dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-950/70 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95 border border-transparent"
                                                    >
                                                        Edit
                                                    </button>
                                                    <x-alert-dialog>
                                                        <x-slot:trigger>
                                                            <button type="button" class="inline-flex items-center min-h-9 px-2.5 py-1.5 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 hover:bg-rose-100 dark:hover:bg-rose-950/70 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95 border border-transparent">
                                                                Delete
                                                            </button>
                                                        </x-slot:trigger>

                                                        <x-slot:icon>
                                                            <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                            </svg>
                                                        </x-slot:icon>

                                                        <x-slot:title>Delete News Article</x-slot:title>

                                                        <x-slot:description>
                                                            Are you sure you want to permanently delete "{{ $article->title }}"? This will remove it from the homepage feed. This action cannot be undone.
                                                        </x-slot:description>

                                                        <x-slot:footer>
                                                            <button type="button" @click="open = false" class="btn-outline text-xs py-2 px-4 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                                                Cancel
                                                            </button>
                                                            <form method="POST" action="{{ route('admin.news.destroy', $article->id) }}" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2 px-4 rounded-xl text-xs transition active:scale-95 shadow-sm border border-transparent">
                                                                    Confirm Delete
                                                                </button>
                                                            </form>
                                                        </x-slot:footer>
                                                    </x-alert-dialog>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                        {{ $articles->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Mobile FAB: Add News --}}
        @if(Auth::user()->isAdmin())
            <div class="fixed bottom-0 inset-x-0 z-20 md:hidden pointer-events-none px-4 pb-[max(1rem,env(safe-area-inset-bottom))]">
                <button
                    type="button"
                    @click="openAddArticleModal = true"
                    class="pointer-events-auto w-full inline-flex items-center justify-center gap-2 min-h-[3.25rem] btn-primary text-xs font-bold uppercase tracking-wider shadow-lg shadow-blue-900/20 rounded-2xl"
                    aria-label="Create new article"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Create News Article</span>
                </button>
            </div>
        @endif
    </div>

    {{-- Add Article modal --}}
    @if(Auth::user()->isAdmin())
        <template x-teleport="body">
            <div
                x-show="openAddArticleModal"
                class="fixed inset-0 z-50 flex items-end md:items-center justify-center md:p-4"
                data-overscroll-lock="true"
                role="dialog"
                aria-modal="true"
                aria-labelledby="add-article-title"
                x-cloak
            >
                {{-- Backdrop --}}
                <div
                    x-show="openAddArticleModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm"
                    @click="openAddArticleModal = false"
                    aria-hidden="true"
                ></div>

                {{-- Modal panel --}}
                <div
                    x-show="openAddArticleModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    class="relative z-50 w-full md:max-w-3xl max-h-[92dvh] md:max-h-[90vh] flex flex-col bg-white dark:bg-slate-900 rounded-t-3xl md:rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden"
                    @click.outside="openAddArticleModal = false"
                >
                    <div class="md:hidden flex justify-center pt-3 pb-1 shrink-0" aria-hidden="true">
                        <div class="w-10 h-1 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                    </div>

                    <div class="flex items-center justify-between px-4 md:px-8 py-3 md:py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                        <div class="space-y-0.5 min-w-0 pr-4">
                            <span class="text-[9px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">New Article</span>
                            <h2 id="add-article-title" class="text-base font-black text-slate-800 dark:text-slate-100 font-display uppercase tracking-wide truncate">Publish News Article</h2>
                        </div>
                        <button
                            type="button"
                            @click="openAddArticleModal = false"
                            class="inline-flex items-center justify-center min-w-11 min-h-11 rounded-xl text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition shrink-0"
                            aria-label="Close dialog"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form
                        method="POST"
                        action="{{ route('admin.news.store') }}"
                        enctype="multipart/form-data"
                        class="flex-1 overflow-y-auto overscroll-y-contain px-4 md:px-8 py-4 space-y-4"
                    >
                        @csrf

                        <div class="space-y-1">
                            <label for="article-title" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Article Title</label>
                            <input
                                id="article-title"
                                type="text"
                                name="title"
                                required
                                placeholder="Enter article headline..."
                                value="{{ old('title') }}"
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                            >
                            @error('title')
                                <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label for="article-category" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Category</label>
                                <input
                                    id="article-category"
                                    type="text"
                                    name="category"
                                    required
                                    placeholder="e.g. Swimming, Agriculture, Education"
                                    value="{{ old('category') }}"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                                @error('category')
                                    <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label for="article-read-time" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Estimated Read Time (Minutes)</label>
                                <input
                                    id="article-read-time"
                                    type="number"
                                    name="read_time"
                                    required
                                    min="1"
                                    placeholder="e.g. 5"
                                    value="{{ old('read_time', 5) }}"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                                @error('read_time')
                                    <span class="text-rose-650 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label for="article-excerpt" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Short Summary (Excerpt)</label>
                            <textarea
                                id="article-excerpt"
                                name="excerpt"
                                required
                                rows="2"
                                placeholder="Teaser summary shown on list cards..."
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs min-h-[4rem]"
                            >{{ old('excerpt') }}</textarea>
                            @error('excerpt')
                                <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="article-content" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Full Article Content</label>
                            <textarea
                                id="article-content"
                                name="content"
                                required
                                rows="6"
                                placeholder="Write the full story body content here..."
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs min-h-[10rem]"
                            >{{ old('content') }}</textarea>
                            @error('content')
                                <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                            <div class="flex items-start gap-2.5">
                                <input
                                    type="checkbox"
                                    id="article-featured"
                                    name="is_featured"
                                    value="1"
                                    {{ old('is_featured') ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-650 border-slate-350 dark:border-slate-700 dark:bg-slate-950 rounded focus:ring-blue-500 mt-0.5 shrink-0"
                                >
                                <div>
                                    <label for="article-featured" class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block cursor-pointer select-none">Set as Main Featured Article</label>
                                    <span class="text-[9px] text-slate-400 dark:text-slate-500 mt-0.5 block leading-normal">Large hero card on the top left of newsroom. Max 1 active.</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5">
                                <input
                                    type="checkbox"
                                    id="article-trending"
                                    name="is_trending"
                                    value="1"
                                    {{ old('is_trending') ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-650 border-slate-350 dark:border-slate-700 dark:bg-slate-950 rounded focus:ring-blue-500 mt-0.5 shrink-0"
                                >
                                <div>
                                    <label for="article-trending" class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block cursor-pointer select-none">Display in Trending News</label>
                                    <span class="text-[9px] text-slate-400 dark:text-slate-500 mt-0.5 block leading-normal">Adds article to the trending cards slider block.</span>
                                </div>
                            </div>
                        </div>

                        {{-- Drag and drop uploader at the bottom, 96% width inside container --}}
                        <div class="space-y-1.5 w-[96%] mx-auto pt-2">
                            <label for="article-image" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Cover / Feature Photo</label>
                            <x-file-upload name="image" id="article-image" required="true" accept="image/*" placeholder="Drag cover photo here or click to browse." />
                            <span class="text-[9px] text-slate-400 dark:text-slate-500 block mt-0.5 leading-relaxed">Recommended ratio: 16:9 (max 4MB). Supported: JPG, PNG, WEBP.</span>
                            @error('image')
                                <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Sticky footer actions --}}
                        <div class="sticky bottom-0 -mx-4 md:-mx-8 px-4 md:px-8 py-4 border-t border-slate-100 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pb-[max(0.5rem,env(safe-area-inset-bottom))]">
                            <button
                                type="button"
                                @click="openAddArticleModal = false"
                                class="btn-outline text-xs py-2.5 px-6 min-h-11 w-full sm:w-auto dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                            >
                                Cancel
                            </button>
                            <button type="submit" class="btn-primary text-xs py-2.5 px-6 min-h-11 w-full sm:w-auto">
                                Publish Article
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif

    {{-- Edit Article modal --}}
    @if(Auth::user()->isAdmin())
        <template x-teleport="body">
            <div
                x-show="openEditArticleModal"
                class="fixed inset-0 z-50 flex items-end md:items-center justify-center md:p-4"
                data-overscroll-lock="true"
                role="dialog"
                aria-modal="true"
                aria-labelledby="edit-article-title"
                x-cloak
            >
                {{-- Backdrop --}}
                <div
                    x-show="openEditArticleModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm"
                    @click="openEditArticleModal = false"
                    aria-hidden="true"
                ></div>

                {{-- Modal panel --}}
                <div
                    x-show="openEditArticleModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    class="relative z-50 w-full md:max-w-3xl max-h-[92dvh] md:max-h-[90vh] flex flex-col bg-white dark:bg-slate-900 rounded-t-3xl md:rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden"
                    @click.outside="openEditArticleModal = false"
                >
                    <div class="md:hidden flex justify-center pt-3 pb-1 shrink-0" aria-hidden="true">
                        <div class="w-10 h-1 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                    </div>

                    <div class="flex items-center justify-between px-4 md:px-8 py-3 md:py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                        <div class="space-y-0.5 min-w-0 pr-4">
                            <span class="text-[9px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">Modify Article</span>
                            <h2 id="edit-article-title" class="text-base font-black text-slate-800 dark:text-slate-100 font-display uppercase tracking-wide truncate">Edit News Article</h2>
                        </div>
                        <button
                            type="button"
                            @click="openEditArticleModal = false"
                            class="inline-flex items-center justify-center min-w-11 min-h-11 rounded-xl text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition shrink-0"
                            aria-label="Close dialog"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form
                        method="POST"
                        :action="editFormAction"
                        enctype="multipart/form-data"
                        class="flex-1 overflow-y-auto overscroll-y-contain px-4 md:px-8 py-4 space-y-4"
                    >
                        @csrf
                        @method('PUT')

                        <div class="space-y-1">
                            <label for="edit-article-title" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Article Title</label>
                            <input
                                id="edit-article-title"
                                type="text"
                                name="title"
                                required
                                x-model="editArticleData.title"
                                placeholder="Enter article headline..."
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                            >
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label for="edit-article-category" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Category</label>
                                <input
                                    id="edit-article-category"
                                    type="text"
                                    name="category"
                                    required
                                    x-model="editArticleData.category"
                                    placeholder="e.g. Swimming, Agriculture, Education"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                            </div>

                            <div class="space-y-1">
                                <label for="edit-article-read-time" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Estimated Read Time (Minutes)</label>
                                <input
                                    id="edit-article-read-time"
                                    type="number"
                                    name="read_time"
                                    required
                                    min="1"
                                    x-model="editArticleData.read_time"
                                    placeholder="e.g. 5"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label for="edit-article-excerpt" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Short Summary (Excerpt)</label>
                            <textarea
                                id="edit-article-excerpt"
                                name="excerpt"
                                required
                                rows="2"
                                x-model="editArticleData.excerpt"
                                placeholder="Teaser summary shown on list cards..."
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs min-h-[4rem]"
                            ></textarea>
                        </div>

                        <div class="space-y-1">
                            <label for="edit-article-content" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Full Article Content</label>
                            <textarea
                                id="edit-article-content"
                                name="content"
                                required
                                rows="6"
                                x-model="editArticleData.content"
                                placeholder="Write the full story body content here..."
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs min-h-[10rem]"
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                            <div class="flex items-start gap-2.5">
                                <input
                                    type="checkbox"
                                    id="edit-article-featured"
                                    name="is_featured"
                                    value="1"
                                    x-model="editArticleData.is_featured"
                                    class="w-4 h-4 text-blue-650 border-slate-350 dark:border-slate-700 dark:bg-slate-950 rounded focus:ring-blue-500 mt-0.5 shrink-0"
                                >
                                <div>
                                    <label for="edit-article-featured" class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block cursor-pointer select-none">Set as Main Featured Article</label>
                                    <span class="text-[9px] text-slate-400 dark:text-slate-500 mt-0.5 block leading-normal">Large hero card on the top left of newsroom. Max 1 active.</span>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5">
                                <input
                                    type="checkbox"
                                    id="edit-article-trending"
                                    name="is_trending"
                                    value="1"
                                    x-model="editArticleData.is_trending"
                                    class="w-4 h-4 text-blue-650 border-slate-350 dark:border-slate-700 dark:bg-slate-950 rounded focus:ring-blue-500 mt-0.5 shrink-0"
                                >
                                <div>
                                    <label for="edit-article-trending" class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block cursor-pointer select-none">Display in Trending News</label>
                                    <span class="text-[9px] text-slate-400 dark:text-slate-500 mt-0.5 block leading-normal">Adds article to the trending cards slider block.</span>
                                </div>
                            </div>
                        </div>

                        {{-- Drag and drop uploader at the bottom, 96% width inside container --}}
                        <div class="space-y-1.5 w-[96%] mx-auto pt-2">
                            <label for="edit-article-image" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Cover Photo (Optional)</label>
                            <x-file-upload name="image" id="edit-article-image" required="false" accept="image/*" placeholder="Drag new cover photo here or click to browse." />
                            <div class="mt-2 flex items-center gap-3">
                                <div class="w-16 h-10 rounded overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 flex items-center justify-center">
                                    <template x-if="editArticleData.image_url">
                                        <img :src="editArticleData.image_url" class="w-full h-full object-cover" alt="Active Cover">
                                    </template>
                                    <template x-if="!editArticleData.image_url">
                                        <span class="text-slate-300 text-xs">None</span>
                                    </template>
                                </div>
                                <span class="text-[9px] text-slate-400 dark:text-slate-500 leading-tight">Currently active image. Leave empty to keep it.</span>
                            </div>
                        </div>

                        {{-- Sticky footer actions --}}
                        <div class="sticky bottom-0 -mx-4 md:-mx-8 px-4 md:px-8 py-4 border-t border-slate-100 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pb-[max(0.5rem,env(safe-area-inset-bottom))]">
                            <button
                                type="button"
                                @click="openEditArticleModal = false"
                                class="btn-outline text-xs py-2.5 px-6 min-h-11 w-full sm:w-auto dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                            >
                                Cancel
                            </button>
                            <button type="submit" class="btn-primary text-xs py-2.5 px-6 min-h-11 w-full sm:w-auto">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif

</div>

{{-- Mobile interaction helpers --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('newsAdmin', (config = {}) => ({
            openAddArticleModal: config.openOnLoad ?? false,
            openEditArticleModal: false,
            editArticleData: { id: null, title: '', category: '', read_time: 5, excerpt: '', content: '', is_featured: false, is_trending: false, image_url: '' },
            editFormAction: '',

            init() {
                if (this.openAddArticleModal) {
                    this.lockBodyScroll(true);
                }

                this.$watch('openAddArticleModal', (open) => {
                    this.lockBodyScroll(open);
                });

                this.$watch('openEditArticleModal', (open) => {
                    this.lockBodyScroll(open);
                });
            },

            editArticle(article) {
                this.editArticleData = {
                    id: article.id,
                    title: article.title,
                    category: article.category,
                    read_time: article.read_time,
                    excerpt: article.excerpt,
                    content: article.content,
                    is_featured: !!article.is_featured,
                    is_trending: !!article.is_trending,
                    image_url: article.image_url
                };
                this.editFormAction = `/admin/news/${article.id}`;
                this.openEditArticleModal = true;
            },

            lockBodyScroll(locked) {
                document.documentElement.classList.toggle('overflow-hidden', locked);
                document.body.classList.toggle('overflow-hidden', locked);
                document.body.classList.toggle('touch-none', locked);
            },
        }));
    });
</script>
@endsection
