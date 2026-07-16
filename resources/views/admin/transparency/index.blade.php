@extends('layouts.app')

@section('content')
<div
    x-data="transparencyAdmin({
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
                    <li class="text-slate-800 dark:text-slate-200 truncate" aria-current="page">Transparency Board</li>
                </ol>
            </nav>

            {{-- Page header --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="space-y-1 min-w-0">
                    <span class="text-[10px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">Open Governance</span>
                    <h1 class="text-xl md:text-2xl font-black tracking-tight text-slate-800 dark:text-slate-100 font-display uppercase">Manage Transparency Posts</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Publish budget reports, resolutions, and public disclosures for citizens.</p>
                </div>
                @if(Auth::user()->isAdmin())
                    <button
                        type="button"
                        @click="openAddPostModal = true"
                        class="hidden sm:inline-flex btn-primary text-xs shrink-0 items-center gap-2 min-h-11"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Post Document</span>
                    </button>
                @endif
            </div>

            {{-- Transparency Container --}}
            <div class="card p-0 overflow-hidden bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl md:rounded-3xl shadow-sm">

                @if($posts->isEmpty())
                    <div class="text-center py-12 md:py-16 px-4 space-y-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">No Transparency Posts</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">Publish the first public disclosure document.</p>
                        </div>
                    </div>
                @else

                    {{-- Mobile View --}}
                    <ul class="md:hidden divide-y divide-slate-100 dark:divide-slate-800" role="list" aria-label="Disclosures">
                        @foreach($posts as $post)
                            <li class="p-4 space-y-3">
                                <div class="flex items-start justify-between gap-2 min-w-0">
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-snug truncate">{{ $post->title }}</h3>
                                    @if($post->is_active)
                                        <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 rounded text-[9px] font-extrabold uppercase tracking-wide">Live</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-slate-150 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded text-[9px] font-extrabold uppercase tracking-wide">Hidden</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 text-[10px] text-slate-400 dark:text-slate-500 font-mono">
                                    <span class="uppercase font-bold text-[#1e40af] dark:text-blue-400">{{ $post->categoryLabel() }}</span>
                                    <span>·</span>
                                    <span>{{ $post->published_at?->format('M d, Y') ?? 'Draft' }}</span>
                                </div>

                                <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-50 dark:border-slate-800">
                                    <div class="text-[10px]">
                                        @if($post->file_path)
                                            <span class="text-emerald-600 dark:text-emerald-400 font-medium">📎 Attachment attached</span>
                                        @else
                                            <span class="text-slate-400">No Attachment</span>
                                        @endif
                                    </div>

                                    @if(Auth::user()->isAdmin())
                                        <div class="flex items-center gap-2">
                                            <button
                                                type="button"
                                                @click="editPost({ id: {{ $post->id }}, title: '{{ addslashes($post->title) }}', category: '{{ addslashes($post->category) }}', excerpt: '{{ addslashes(str_replace(["\r", "\n"], ' ', $post->excerpt)) }}', content: '{{ addslashes(str_replace(["\r", "\n"], ' ', $post->content ?? '')) }}', is_active: {{ $post->is_active ? 1 : 0 }}, image_url: '{{ $post->image_path ? asset('storage/' . $post->image_path) : '' }}', file_url: '{{ $post->file_path ? asset('storage/' . $post->file_path) : '' }}' })"
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

                                                <x-slot:title>Delete Post</x-slot:title>

                                                <x-slot:description>
                                                    Are you sure you want to permanently delete this transparency post? This action cannot be undone.
                                                </x-slot:description>

                                                <x-slot:footer>
                                                    <button type="button" @click="open = false" class="btn-outline text-xs py-2 px-4 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                                        Cancel
                                                    </button>
                                                    <form method="POST" action="{{ route('admin.transparency.destroy', $post->id) }}" class="inline">
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

                    {{-- Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-700 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider font-display">
                                    <th class="p-4 pl-6">Title</th>
                                    <th class="p-4">Category</th>
                                    <th class="p-4">Published</th>
                                    <th class="p-4">File Attachment</th>
                                    <th class="p-4">Status</th>
                                    @if(Auth::user()->isAdmin())
                                        <th class="p-4 pr-6 text-right">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-600 dark:text-slate-350">
                                @foreach($posts as $post)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition duration-150">
                                        <td class="p-4 pl-6 font-bold text-slate-800 dark:text-slate-100 max-w-xs truncate">{{ $post->title }}</td>
                                        <td class="p-4 text-slate-650 dark:text-slate-400 capitalize">{{ $post->categoryLabel() }}</td>
                                        <td class="p-4 text-slate-500 dark:text-slate-500">{{ $post->published_at?->format('M d, Y') ?? '—' }}</td>
                                        <td class="p-4 font-bold">
                                            @if($post->file_path)
                                                <a href="{{ asset('storage/' . $post->file_path) }}" target="_blank" class="text-blue-650 dark:text-blue-400 hover:underline">Download File</a>
                                            @else
                                                <span class="text-slate-300 dark:text-slate-700">—</span>
                                            @endif
                                        </td>
                                        <td class="p-4">
                                            @if($post->is_active)
                                                <span class="px-2.5 py-0.5 bg-emerald-100 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 rounded text-[9px] font-extrabold uppercase tracking-wide font-display">Live</span>
                                            @else
                                                <span class="px-2.5 py-0.5 bg-slate-150 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded text-[9px] font-extrabold uppercase tracking-wide font-display">Hidden</span>
                                            @endif
                                        </td>
                                        @if(Auth::user()->isAdmin())
                                            <td class="p-4 pr-6 text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-2">
                                                    @if($post->is_active && $post->published_at)
                                                        <a href="{{ route('transparency.show', $post->slug) }}" target="_blank" class="inline-flex items-center min-h-9 px-2.5 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95 border border-transparent">View</a>
                                                    @endif
                                                    <button
                                                        type="button"
                                                        @click="editPost({ id: {{ $post->id }}, title: '{{ addslashes($post->title) }}', category: '{{ addslashes($post->category) }}', excerpt: '{{ addslashes(str_replace(["\r", "\n"], ' ', $post->excerpt)) }}', content: '{{ addslashes(str_replace(["\r", "\n"], ' ', $post->content ?? '')) }}', is_active: {{ $post->is_active ? 1 : 0 }}, image_url: '{{ $post->image_path ? asset('storage/' . $post->image_path) : '' }}', file_url: '{{ $post->file_path ? asset('storage/' . $post->file_path) : '' }}' })"
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

                                                        <x-slot:title>Delete Post</x-slot:title>

                                                        <x-slot:description>
                                                            Are you sure you want to permanently delete this transparency post? This action cannot be undone.
                                                        </x-slot:description>

                                                        <x-slot:footer>
                                                            <button type="button" @click="open = false" class="btn-outline text-xs py-2 px-4 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800">
                                                                Cancel
                                                            </button>
                                                            <form method="POST" action="{{ route('admin.transparency.destroy', $post->id) }}" class="inline">
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
                    @if($posts->hasPages())
                        <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/30">
                            {{ $posts->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Mobile FAB: Add Post --}}
        @if(Auth::user()->isAdmin())
            <div class="fixed bottom-0 inset-x-0 z-20 md:hidden pointer-events-none px-4 pb-[max(1rem,env(safe-area-inset-bottom))]">
                <button
                    type="button"
                    @click="openAddPostModal = true"
                    class="pointer-events-auto w-full inline-flex items-center justify-center gap-2 min-h-[3.25rem] btn-primary text-xs font-bold uppercase tracking-wider shadow-lg shadow-blue-900/20 rounded-2xl"
                    aria-label="Post new disclosure"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Post Document</span>
                </button>
            </div>
        @endif
    </div>

    {{-- Add Transparency Post Modal --}}
    @if(Auth::user()->isAdmin())
        <template x-teleport="body">
            <div
                x-show="openAddPostModal"
                class="fixed inset-0 z-50 flex items-end md:items-center justify-center md:p-4"
                data-overscroll-lock="true"
                role="dialog"
                aria-modal="true"
                aria-labelledby="add-post-title"
                x-cloak
            >
                {{-- Backdrop --}}
                <div
                    x-show="openAddPostModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm"
                    @click="openAddPostModal = false"
                    aria-hidden="true"
                ></div>

                {{-- Modal panel --}}
                <div
                    x-show="openAddPostModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    class="relative z-50 w-full md:max-w-3xl max-h-[92dvh] md:max-h-[90vh] flex flex-col bg-white dark:bg-slate-900 rounded-t-3xl md:rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden"
                    @click.outside="openAddPostModal = false"
                >
                    <div class="md:hidden flex justify-center pt-3 pb-1 shrink-0" aria-hidden="true">
                        <div class="w-10 h-1 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                    </div>

                    <div class="flex items-center justify-between px-4 md:px-8 py-3 md:py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                        <div class="space-y-0.5 min-w-0 pr-4">
                            <span class="text-[9px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">New Post</span>
                            <h2 id="add-post-title" class="text-base font-black text-slate-800 dark:text-slate-100 font-display uppercase tracking-wide truncate">Post Disclosure Document</h2>
                        </div>
                        <button
                            type="button"
                            @click="openAddPostModal = false"
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
                        action="{{ route('admin.transparency.store') }}"
                        enctype="multipart/form-data"
                        class="flex-1 overflow-y-auto overscroll-y-contain px-4 md:px-8 py-4 space-y-4"
                    >
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-1 md:col-span-2">
                                <label for="post-title" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Document Title</label>
                                <input
                                    id="post-title"
                                    type="text"
                                    name="title"
                                    required
                                    placeholder="e.g. FY 2025 SK Budget Appropriation"
                                    value="{{ old('title') }}"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                                @error('title')
                                    <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="space-y-1">
                                <label for="post-category" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Category</label>
                                <select id="post-category" name="category" required class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 text-xs py-2.5 min-h-11">
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}" {{ old('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label for="post-excerpt" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Short Summary (Excerpt)</label>
                            <textarea
                                id="post-excerpt"
                                name="excerpt"
                                required
                                rows="2"
                                placeholder="Brief description shown on transparency cards..."
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs min-h-[4rem]"
                            >{{ old('excerpt') }}</textarea>
                            @error('excerpt')
                                <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="post-content" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Full Content (Optional)</label>
                            <textarea
                                id="post-content"
                                name="content"
                                rows="5"
                                placeholder="Detailed disclosure text (optional)..."
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-950 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs min-h-[8rem]"
                            >{{ old('content') }}</textarea>
                            @error('content')
                                <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex items-start gap-2.5 py-1">
                            <input
                                type="checkbox"
                                id="post-active"
                                name="is_active"
                                value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="w-4 h-4 text-blue-650 border-slate-350 dark:border-slate-700 dark:bg-slate-950 rounded focus:ring-blue-500 mt-0.5 shrink-0"
                            >
                            <div>
                                <label for="post-active" class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block cursor-pointer select-none">Publish on Public Transparency Board</label>
                                <span class="text-[9px] text-slate-400 dark:text-slate-500 mt-0.5 block leading-normal">Allows citizens to search and view this post instantly.</span>
                            </div>
                        </div>

                        {{-- Drag and drop uploaders at the bottom, 96% width inside container --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-150 dark:border-slate-800">
                            <div class="space-y-1.5 w-[96%] mx-auto">
                                <label for="post-image" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Cover Image (Optional)</label>
                                <x-file-upload name="image" id="post-image" accept="image/jpeg,image/png,image/webp" placeholder="Drag cover image here or click to browse." />
                                @error('image')
                                    <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="space-y-1.5 w-[96%] mx-auto">
                                <label for="post-document" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Attach File (PDF/DOC, Optional)</label>
                                <x-file-upload name="document" id="post-document" accept=".pdf,.doc,.docx,.xls,.xlsx,image/*" placeholder="Drag file here or click to browse." />
                                <span class="text-[9px] text-slate-400 dark:text-slate-500 block mt-0.5 leading-normal">Max 8MB. PDF, Word, Excel, or image.</span>
                                @error('document')
                                    <span class="text-rose-600 dark:text-rose-400 text-[10px] font-semibold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Sticky footer actions --}}
                        <div class="sticky bottom-0 -mx-4 md:-mx-8 px-4 md:px-8 py-4 border-t border-slate-100 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pb-[max(0.5rem,env(safe-area-inset-bottom))]">
                            <button
                                type="button"
                                @click="openAddPostModal = false"
                                class="btn-outline text-xs py-2.5 px-6 min-h-11 w-full sm:w-auto dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                            >
                                Cancel
                            </button>
                            <button type="submit" class="btn-primary text-xs py-2.5 px-6 min-h-11 w-full sm:w-auto">
                                Publish Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    @endif

    {{-- Edit Transparency Post Modal --}}
    @if(Auth::user()->isAdmin())
        <template x-teleport="body">
            <div
                x-show="openEditPostModal"
                class="fixed inset-0 z-50 flex items-end md:items-center justify-center md:p-4"
                data-overscroll-lock="true"
                role="dialog"
                aria-modal="true"
                aria-labelledby="edit-post-title"
                x-cloak
            >
                {{-- Backdrop --}}
                <div
                    x-show="openEditPostModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm"
                    @click="openEditPostModal = false"
                    aria-hidden="true"
                ></div>

                {{-- Modal panel --}}
                <div
                    x-show="openEditPostModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 md:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-full md:translate-y-4 md:scale-95"
                    class="relative z-50 w-full md:max-w-3xl max-h-[92dvh] md:max-h-[90vh] flex flex-col bg-white dark:bg-slate-900 rounded-t-3xl md:rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl overflow-hidden"
                    @click.outside="openEditPostModal = false"
                >
                    <div class="md:hidden flex justify-center pt-3 pb-1 shrink-0" aria-hidden="true">
                        <div class="w-10 h-1 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                    </div>

                    <div class="flex items-center justify-between px-4 md:px-8 py-3 md:py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                        <div class="space-y-0.5 min-w-0 pr-4">
                            <span class="text-[9px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">Modify Post</span>
                            <h2 id="edit-post-title" class="text-base font-black text-slate-800 dark:text-slate-100 font-display uppercase tracking-wide truncate">Edit Transparency Post</h2>
                        </div>
                        <button
                            type="button"
                            @click="openEditPostModal = false"
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

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="space-y-1 md:col-span-2">
                                <label for="edit-post-title" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Document Title</label>
                                <input
                                    id="edit-post-title"
                                    type="text"
                                    name="title"
                                    required
                                    x-model="editPostData.title"
                                    placeholder="e.g. FY 2025 SK Budget Appropriation"
                                    class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs py-2.5 min-h-11"
                                >
                            </div>

                            <div class="space-y-1">
                                <label for="edit-post-category" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Category</label>
                                <select id="edit-post-category" name="category" required x-model="editPostData.category" class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 text-xs py-2.5 min-h-11">
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label for="edit-post-excerpt" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Short Summary (Excerpt)</label>
                            <textarea
                                id="edit-post-excerpt"
                                name="excerpt"
                                required
                                rows="2"
                                x-model="editPostData.excerpt"
                                placeholder="Brief description shown on transparency cards..."
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs min-h-[4rem]"
                            ></textarea>
                        </div>

                        <div class="space-y-1">
                            <label for="edit-post-content" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Full Content (Optional)</label>
                            <textarea
                                id="edit-post-content"
                                name="content"
                                rows="5"
                                x-model="editPostData.content"
                                placeholder="Detailed disclosure text (optional)..."
                                class="field focus:ring-4 focus:ring-blue-600/10 dark:bg-slate-955 dark:border-slate-700 dark:text-slate-100 dark:placeholder:text-slate-500 text-xs min-h-[8rem]"
                            ></textarea>
                        </div>

                        <div class="flex items-start gap-2.5 py-1">
                            <input
                                type="checkbox"
                                id="edit-post-active"
                                name="is_active"
                                value="1"
                                x-model="editPostData.is_active"
                                class="w-4 h-4 text-blue-650 border-slate-350 dark:border-slate-700 dark:bg-slate-950 rounded focus:ring-blue-500 mt-0.5 shrink-0"
                            >
                            <div>
                                <label for="edit-post-active" class="text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block cursor-pointer select-none">Publish on Public Transparency Board</label>
                                <span class="text-[9px] text-slate-400 dark:text-slate-500 mt-0.5 block leading-normal">Allows citizens to search and view this post instantly.</span>
                            </div>
                        </div>

                        {{-- Drag and drop uploaders at the bottom, 96% width inside container --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-150 dark:border-slate-800">
                            <div class="space-y-1.5 w-[96%] mx-auto">
                                <label for="edit-post-image" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Cover Image (Optional)</label>
                                <x-file-upload name="image" id="edit-post-image" required="false" accept="image/jpeg,image/png,image/webp" placeholder="Drag new cover image here or click to browse." />
                                <div class="mt-2 flex items-center gap-3">
                                    <div class="w-16 h-10 rounded overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 flex items-center justify-center">
                                        <template x-if="editPostData.image_url">
                                            <img :src="editPostData.image_url" class="w-full h-full object-cover" alt="Active Cover">
                                        </template>
                                        <template x-if="!editPostData.image_url">
                                            <span class="text-slate-300 text-xs">None</span>
                                        </template>
                                    </div>
                                    <span class="text-[9px] text-slate-400 dark:text-slate-500 leading-tight">Current cover. Leave empty to keep it.</span>
                                </div>
                            </div>

                            <div class="space-y-1.5 w-[96%] mx-auto">
                                <label for="edit-post-document" class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Attach File (PDF/DOC, Optional)</label>
                                <x-file-upload name="document" id="edit-post-document" required="false" accept=".pdf,.doc,.docx,.xls,.xlsx,image/*" placeholder="Drag a new file here or click to browse." />
                                <div class="mt-2 flex items-center gap-3">
                                    <template x-if="editPostData.file_url">
                                        <a :href="editPostData.file_url" target="_blank" class="text-[10px] text-blue-600 dark:text-blue-400 font-bold hover:underline">Download Current File</a>
                                    </template>
                                    <template x-if="!editPostData.file_url">
                                        <span class="text-slate-350 text-[10px] italic">No attached file</span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Sticky footer actions --}}
                        <div class="sticky bottom-0 -mx-4 md:-mx-8 px-4 md:px-8 py-4 border-t border-slate-100 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 pb-[max(0.5rem,env(safe-area-inset-bottom))]">
                            <button
                                type="button"
                                @click="openEditPostModal = false"
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
        Alpine.data('transparencyAdmin', (config = {}) => ({
            openAddPostModal: config.openOnLoad ?? false,
            openEditPostModal: false,
            editPostData: { id: null, title: '', category: '', excerpt: '', content: '', is_active: true, image_url: '', file_url: '' },
            editFormAction: '',

            init() {
                if (this.openAddPostModal) {
                    this.lockBodyScroll(true);
                }

                this.$watch('openAddPostModal', (open) => {
                    this.lockBodyScroll(open);
                });

                this.$watch('openEditPostModal', (open) => {
                    this.lockBodyScroll(open);
                });
            },

            editPost(post) {
                this.editPostData = {
                    id: post.id,
                    title: post.title,
                    category: post.category,
                    excerpt: post.excerpt,
                    content: post.content,
                    is_active: !!post.is_active,
                    image_url: post.image_url,
                    file_url: post.file_url
                };
                this.editFormAction = `/admin/transparency/${post.id}`;
                this.openEditPostModal = true;
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
