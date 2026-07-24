@extends('layouts.app')

@section('content')
<div
    x-data="announcementsAdmin({
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

        {{-- Scrollable page body --}}
        <div class="flex-1 overflow-y-auto overscroll-y-contain p-4 md:p-8 space-y-4 md:space-y-6 pb-24 md:pb-8">

            {{-- Breadcrumbs --}}
            <nav aria-label="Breadcrumb" class="flex items-center pb-3 md:pb-4 border-b border-slate-100 dark:border-slate-800">
                <ol class="flex items-center gap-2 text-[10px] md:text-xs font-semibold uppercase tracking-wider min-w-0">
                    <li class="shrink-0">
                        <a href="{{ route('dashboard.index') }}" class="text-slate-400 dark:text-slate-500 hover:text-[#1e40af] dark:hover:text-blue-400 transition duration-150">Dashboard</a>
                    </li>
                    <li class="text-slate-300 dark:text-slate-600 shrink-0" aria-hidden="true">/</li>
                    <li class="text-slate-800 dark:text-slate-200 truncate" aria-current="page">Announcements</li>
                </ol>
            </nav>

            {{-- Page header --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="space-y-1 min-w-0">
                    <span class="text-[10px] font-black text-[#1e40af] dark:text-blue-400 uppercase tracking-widest block font-display">Announcements Board</span>
                    <h1 class="text-xl md:text-2xl font-black tracking-tight text-slate-800 dark:text-slate-100 font-display uppercase">Manage Announcements</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">Broadcast updates, system warnings, active rules, and public announcements to citizens.</p>
                </div>
                <button
                    type="button"
                    @click="openAddModal = true"
                    class="hidden sm:inline-flex btn-primary text-xs shrink-0 items-center gap-2 min-h-11"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Create Announcement</span>
                </button>
            </div>

            {{-- Announcements Card --}}
            <div class="card p-0 overflow-hidden bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl md:rounded-3xl shadow-sm">

                @if($announcements->isEmpty())
                    <div class="text-center py-12 md:py-16 px-4 space-y-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">No Announcements Published</h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">Click "Create Announcement" above to broadcast your first announcement on the citizen homepage.</p>
                        </div>
                    </div>
                @else

                    {{-- Mobile view --}}
                    <ul class="md:hidden divide-y divide-slate-100 dark:divide-slate-800" role="list" aria-label="Announcements">
                        @foreach($announcements as $ann)
                            <li class="p-4 space-y-3">
                                <div class="flex-1 min-w-0 space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-wider
                                            @if($ann->type === 'success') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                            @elseif($ann->type === 'warning') bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300
                                            @else bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300
                                            @endif">
                                            {{ $ann->type }}
                                        </span>
                                        @if(!$ann->is_active)
                                            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-400 rounded text-[8px] font-black uppercase tracking-wider">Inactive</span>
                                        @endif
                                    </div>
                                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 leading-snug">
                                        {{ $ann->title }}
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2">{{ $ann->body }}</p>
                                    <div class="text-[9px] text-slate-400 font-semibold uppercase tracking-wider">
                                        Published: {{ $ann->published_at->format('M d, Y h:i A') }}
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-50 dark:border-slate-800">
                                    <button
                                        type="button"
                                        @click="editAnnouncement({
                                            id: {{ $ann->id }},
                                            title: '{{ addslashes($ann->title) }}',
                                            body: '{{ addslashes(str_replace(["\r", "\n"], ' ', $ann->body)) }}',
                                            type: '{{ $ann->type }}',
                                            is_active: {{ $ann->is_active ? 1 : 0 }},
                                            published_at: '{{ $ann->published_at->format('Y-m-d\TH:i') }}'
                                        })"
                                        class="inline-flex items-center min-h-9 px-3 py-1 bg-blue-50 dark:bg-blue-950/40 text-[#1e40af] dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-950/70 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95"
                                    >
                                        Edit
                                    </button>
                                    <x-alert-dialog>
                                        <x-slot:trigger>
                                            <button
                                                type="button"
                                                class="inline-flex items-center min-h-9 px-3 py-1 bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 hover:bg-rose-100 dark:hover:bg-rose-950/70 font-bold rounded-lg transition text-[10px] uppercase tracking-wider active:scale-95"
                                            >
                                                Delete
                                            </button>
                                        </x-slot:trigger>
                                        <x-slot:icon>
                                            <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        </x-slot:icon>
                                        <x-slot:title>Delete Announcement</x-slot:title>
                                        <x-slot:description>Are you sure you want to delete "{{ $ann->title }}"?</x-slot:description>
                                        <x-slot:footer>
                                            <button type="button" @click="open = false" class="btn-outline text-xs py-2 px-4">Cancel</button>
                                            <form method="POST" action="{{ route('admin.announcements.destroy', $ann->id) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2 px-4 rounded-xl text-xs">Confirm</button>
                                            </form>
                                        </x-slot:footer>
                                    </x-alert-dialog>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Desktop view --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-100 dark:border-slate-700 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider font-display">
                                    <th class="p-4 pl-6">Title</th>
                                    <th class="p-4">Type</th>
                                    <th class="p-4">Active</th>
                                    <th class="p-4">Publish Date</th>
                                    <th class="p-4">Created By</th>
                                    <th class="p-4 pr-6 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-600 dark:text-slate-350">
                                @foreach($announcements as $ann)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition duration-150">
                                        <td class="p-4 pl-6 font-bold text-slate-800 dark:text-slate-100 max-w-xs truncate" title="{{ $ann->title }}">
                                            {{ $ann->title }}
                                        </td>
                                        <td class="p-4">
                                            <span class="px-2.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider
                                                @if($ann->type === 'success') bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300
                                                @elseif($ann->type === 'warning') bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300
                                                @else bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300
                                                @endif">
                                                {{ $ann->type }}
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            @if($ann->is_active)
                                                <span class="text-emerald-500 font-bold uppercase tracking-wider text-[10px]">Active</span>
                                            @else
                                                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="p-4 font-mono font-medium text-slate-500 dark:text-slate-500">
                                            {{ $ann->published_at->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="p-4 font-medium text-slate-700 dark:text-slate-450">
                                            {{ $ann->author ? $ann->author->name : 'System' }}
                                        </td>
                                        <td class="p-4 pr-6 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-2">
                                                <button
                                                    type="button"
                                                    @click="editAnnouncement({
                                                        id: {{ $ann->id }},
                                                        title: '{{ addslashes($ann->title) }}',
                                                        body: '{{ addslashes(str_replace(["\r", "\n"], ' ', $ann->body)) }}',
                                                        type: '{{ $ann->type }}',
                                                        is_active: {{ $ann->is_active ? 1 : 0 }},
                                                        published_at: '{{ $ann->published_at->format('Y-m-d\TH:i') }}'
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
                                                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                    </x-slot:icon>
                                                    <x-slot:title>Delete Announcement</x-slot:title>
                                                    <x-slot:description>Are you sure you want to permanently delete "{{ $ann->title }}"? This will remove it from the citizen broadcast feed.</x-slot:description>
                                                    <x-slot:footer>
                                                        <button type="button" @click="open = false" class="btn-outline text-xs py-2 px-4">Cancel</button>
                                                        <form method="POST" action="{{ route('admin.announcements.destroy', $ann->id) }}" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2 px-4 rounded-xl text-xs">Confirm Delete</button>
                                                        </form>
                                                    </x-slot:footer>
                                                </x-alert-dialog>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                        {{ $announcements->links() }}
                    </div>

                @endif

            </div>

        </div>

    </div>

    {{-- Create Modal --}}
    <x-modal name="open-add-modal" show="openAddModal" focusable>
        <form method="POST" action="{{ route('admin.announcements.store') }}" class="p-6 space-y-6">
            @csrf
            <div>
                <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider font-display">Create Announcement</h2>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Configure broadcast type, publishing schedule, and content body.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="title" class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 block mb-1">Title</label>
                    <input type="text" name="title" id="title" required class="input-field py-2 px-3 text-xs w-full" placeholder="e.g. Silid Karunungan Schedule Active">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 block mb-1">Type</label>
                        <select name="type" id="type" required class="input-field py-2 px-3 text-xs w-full">
                            <option value="info">Info (Blue)</option>
                            <option value="warning">Warning (Amber)</option>
                            <option value="success">Success (Emerald)</option>
                        </select>
                    </div>

                    <div>
                        <label for="published_at" class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 block mb-1">Published At</label>
                        <input type="datetime-local" name="published_at" id="published_at" required value="{{ now()->format('Y-m-d\TH:i') }}" class="input-field py-2 px-3 text-xs w-full">
                    </div>
                </div>

                <div>
                    <label class="inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                        <div class="w-8 h-4 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600 relative"></div>
                        <span class="ml-2 text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Active Broadcast Status</span>
                    </label>
                </div>

                <div>
                    <label for="body" class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 block mb-1">Content Body</label>
                    <textarea name="body" id="body" rows="4" required class="input-field py-2.5 px-3 text-xs w-full resize-none" placeholder="Provide description or broadcast content..."></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button type="button" @click="openAddModal = false" class="btn-outline text-xs py-2 px-4">Cancel</button>
                <button type="submit" class="btn-primary text-xs py-2 px-4">Publish Announcement</button>
            </div>
        </form>
    </x-modal>

    {{-- Edit Modal --}}
    <x-modal name="open-edit-modal" show="openEditModal" focusable>
        <form method="POST" :action="`/admin/announcements/${editId}`" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            <div>
                <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider font-display">Edit Announcement</h2>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 font-semibold">Update properties or content values for the selected announcement.</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="edit_title" class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 block mb-1">Title</label>
                    <input type="text" name="title" id="edit_title" x-model="editTitle" required class="input-field py-2 px-3 text-xs w-full">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="edit_type" class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 block mb-1">Type</label>
                        <select name="type" id="edit_type" x-model="editType" required class="input-field py-2 px-3 text-xs w-full">
                            <option value="info">Info (Blue)</option>
                            <option value="warning">Warning (Amber)</option>
                            <option value="success">Success (Emerald)</option>
                        </select>
                    </div>

                    <div>
                        <label for="edit_published_at" class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 block mb-1">Published At</label>
                        <input type="datetime-local" name="published_at" id="edit_published_at" x-model="editPublishedAt" required class="input-field py-2 px-3 text-xs w-full">
                    </div>
                </div>

                <div>
                    <label class="inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="is_active" value="1" :checked="editIsActive" class="sr-only peer">
                        <div class="w-8 h-4 bg-slate-200 peer-focus:outline-none dark:bg-slate-700 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600 relative"></div>
                        <span class="ml-2 text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Active Broadcast Status</span>
                    </label>
                </div>

                <div>
                    <label for="edit_body" class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 block mb-1">Content Body</label>
                    <textarea name="body" id="edit_body" rows="4" x-model="editBody" required class="input-field py-2.5 px-3 text-xs w-full resize-none"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                <button type="button" @click="openEditModal = false" class="btn-outline text-xs py-2 px-4">Cancel</button>
                <button type="submit" class="btn-primary text-xs py-2 px-4">Save Changes</button>
            </div>
        </form>
    </x-modal>

</div>

<script>
    function announcementsAdmin(config) {
        return {
            mobileSidebar: false,
            openAddModal: false,
            openEditModal: false,
            
            // Edit model bindings
            editId: '',
            editTitle: '',
            editBody: '',
            editType: 'info',
            editIsActive: false,
            editPublishedAt: '',

            init() {
                if (config.openOnLoad) {
                    this.openAddModal = true;
                }
            },

            editAnnouncement(ann) {
                this.editId = ann.id;
                this.editTitle = ann.title;
                this.editBody = ann.body;
                this.editType = ann.type;
                this.editIsActive = !!ann.is_active;
                this.editPublishedAt = ann.published_at;
                this.openEditModal = true;
            }
        }
    }
</script>
@endsection
