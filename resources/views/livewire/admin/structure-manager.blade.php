<div class="space-y-6">
    <!-- Session Messages -->
    @if(session()->has('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-2xl text-xs font-bold font-sans">
            {{ session('success') }}
        </div>
    @endif

    <!-- Navigation Header / Tabs -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-5 border-b border-slate-800">
        <div class="flex items-center space-x-2">
            <button wire:click="selectTab('structure')" 
                    class="px-4 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition {{ $activeAdminTab === 'structure' ? 'bg-[#1e40af] text-white' : 'text-slate-400 hover:text-slate-200' }}">
                Structure Manager
            </button>
            <button wire:click="selectTab('archives')" 
                    class="px-4 py-2 text-xs font-black uppercase tracking-wider rounded-xl transition {{ $activeAdminTab === 'archives' ? 'bg-[#1e40af] text-white' : 'text-slate-400 hover:text-slate-200' }}">
                Archives & Bin
            </button>
        </div>

        @if($activeAdminTab === 'structure')
            <button wire:click="$toggle('newCommitteeName')" 
                    class="btn-primary btn-sm flex items-center space-x-1.5">
                <span>➕ Add Committee</span>
            </button>
        @endif
    </div>

    <!-- Active Tab: Structure Manager -->
    @if($activeAdminTab === 'structure')
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Committees List -->
            <div class="lg:col-span-1 space-y-3 bg-slate-900/60 p-4 rounded-3xl border border-slate-800">
                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest block mb-2 font-display">Committees & Subtopics</span>
                
                <button wire:click="selectCommittee('all')" 
                        class="w-full text-left px-4 py-3 rounded-2xl text-xs font-black uppercase tracking-wider transition {{ $activeCommitteeId === 'all' ? 'bg-slate-800 text-white border border-slate-700' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border border-transparent' }}">
                    📁 All Committees
                </button>

                @foreach($committees as $committee)
                    <div class="group relative">
                        <button wire:click="selectCommittee({{ $committee->id }})" 
                                class="w-full text-left px-4 py-3 rounded-2xl text-xs font-black uppercase tracking-wider transition pr-10 {{ $activeCommitteeId == $committee->id ? 'bg-slate-800 text-white border border-slate-700' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/40 border border-transparent' }}">
                            🏷️ {{ $committee->name }}
                        </button>
                        <button wire:click="confirmDeleteCommittee({{ $committee->id }})" 
                                class="absolute right-2 top-2.5 p-1.5 text-slate-500 hover:text-rose-500 rounded-lg hover:bg-rose-500/10 transition opacity-0 group-hover:opacity-100 focus:opacity-100" 
                                title="Delete Committee">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                @endforeach

                <!-- Add Committee Input Inline -->
                <div class="pt-3 border-t border-slate-850">
                    <form wire:submit.prevent="storeCommittee" class="space-y-2">
                        <input type="text" wire:model="newCommitteeName" placeholder="New Committee name..." class="w-full rounded-xl border border-slate-800 bg-slate-950 px-3 py-2 text-xs text-slate-100 outline-none focus:border-[#1e40af] transition">
                        @error('newCommitteeName')
                            <p class="text-rose-500 text-[10px] font-bold mt-1">{{ $message }}</p>
                        @enderror
                        <button type="submit" class="w-full bg-[#1e40af] hover:bg-blue-700 text-white text-xs font-black uppercase tracking-wider py-2 rounded-xl transition">
                            Save Committee
                        </button>
                    </form>
                </div>
            </div>

            <!-- Initiatives Listing -->
            <div class="lg:col-span-3 space-y-6">
                @foreach($committees as $committee)
                    @if($activeCommitteeId === 'all' || $activeCommitteeId == $committee->id)
                        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-4">
                            <div class="flex justify-between items-center pb-3 border-b border-slate-800">
                                <div>
                                    <span class="text-[9px] font-black text-blue-400 uppercase tracking-widest block font-display">Committee Group</span>
                                    <h3 class="text-sm font-black text-white uppercase tracking-wider font-display">{{ $committee->name }}</h3>
                                </div>
                                <button wire:click="openAddInitiative({{ $committee->id }})" class="btn-success btn-sm flex items-center space-x-1">
                                    <span>➕ Add Initiative</span>
                                </button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs font-medium text-slate-400">
                                    <thead>
                                        <tr class="border-b border-slate-800 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                            <th class="py-3 px-4">Title / Program</th>
                                            <th class="py-3 px-4">Status</th>
                                            <th class="py-3 px-4">Dynamic Fields</th>
                                            <th class="py-3 px-4 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/40">
                                        @forelse($committee->initiatives as $initiative)
                                            <tr class="hover:bg-slate-850/20 transition-colors">
                                                <td class="py-3.5 px-4 font-bold text-white">
                                                    <div>{{ $initiative->title }}</div>
                                                    <div class="text-[10px] text-slate-500 font-normal line-clamp-1 mt-0.5">{{ $initiative->description }}</div>
                                                </td>
                                                <td class="py-3.5 px-4">
                                                    @if($initiative->is_coming_soon)
                                                        <span class="inline-flex px-2 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[9px] font-black uppercase tracking-wider">Draft</span>
                                                    @else
                                                        <span class="inline-flex px-2 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[9px] font-black uppercase tracking-wider">Published</span>
                                                    @endif
                                                </td>
                                                <td class="py-3.5 px-4">
                                                    @php $fieldCount = count($initiative->form_structure ?? $initiative->custom_fields ?? []); @endphp
                                                    <span class="text-[10px] font-bold text-slate-400">{{ $fieldCount }} custom fields</span>
                                                </td>
                                                <td class="py-3.5 px-4 text-right space-x-1.5">
                                                    <!-- Actions Column Button Trio -->
                                                    <button wire:click="editBuilder({{ $initiative->id }})" 
                                                            class="px-2.5 py-1.5 bg-[#1e40af]/15 border border-[#1e40af]/30 hover:bg-[#1e40af] text-blue-400 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-wider transition active:scale-95">
                                                        🔧 Edit Builder
                                                    </button>
                                                    <button wire:click="editInfo({{ $initiative->id }})" 
                                                            class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-750 hover:border-slate-600 rounded-xl text-[10px] font-black uppercase tracking-wider transition active:scale-95">
                                                        📝 Edit Info
                                                    </button>
                                                    <button wire:click="confirmDeleteInitiative({{ $initiative->id }})" 
                                                            class="px-2.5 py-1.5 bg-rose-500/10 border border-rose-500/20 hover:bg-rose-500 text-rose-400 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-wider transition active:scale-95">
                                                        🗑️ Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="py-6 text-center text-slate-500 text-[11px]">
                                                    No initiatives registered under this committee.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Active Tab: Archives -->
    @if($activeAdminTab === 'archives')
        <div class="space-y-6">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-4">
                <h3 class="text-sm font-black text-white uppercase tracking-wider font-display">Deleted Committees</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs font-medium text-slate-400">
                        <thead>
                            <tr class="border-b border-slate-800 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-3 px-4">Committee Name</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/40">
                            @forelse($archivedCommittees as $ac)
                                <tr>
                                    <td class="py-3.5 px-4 font-bold text-white">{{ $ac->name }}</td>
                                    <td class="py-3.5 px-4 text-right space-x-1">
                                        <button wire:click="restoreCommittee({{ $ac->id }})" class="btn-primary btn-sm">Restore</button>
                                        <button wire:click="forceDeleteCommittee({{ $ac->id }})" class="btn-outline btn-sm hover:bg-rose-500 hover:text-white">Delete Permanently</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-6 text-center text-slate-500">No deleted committees.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 space-y-4">
                <h3 class="text-sm font-black text-white uppercase tracking-wider font-display">Deleted Initiatives</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs font-medium text-slate-400">
                        <thead>
                            <tr class="border-b border-slate-800 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="py-3 px-4">Initiative Title</th>
                                <th class="py-3 px-4">Original Committee</th>
                                <th class="py-3 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/40">
                            @forelse($archivedInitiatives as $ai)
                                <tr>
                                    <td class="py-3.5 px-4 font-bold text-white">{{ $ai->title }}</td>
                                    <td class="py-3.5 px-4 text-slate-400">{{ $ai->committee->name ?? 'N/A' }}</td>
                                    <td class="py-3.5 px-4 text-right space-x-1">
                                        <button wire:click="restoreInitiative({{ $ai->id }})" class="btn-primary btn-sm">Restore</button>
                                        <button wire:click="forceDeleteInitiative({{ $ai->id }})" class="btn-outline btn-sm hover:bg-rose-500 hover:text-white">Delete Permanently</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-slate-500">No deleted initiatives.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 1: Edit Info / Add Initiative Modal (Dark-themed, Slate-800) -->
    @if($showEditInfoModal || $showAddInitiativeModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" wire:click="$set('showEditInfoModal', false); wire:click='$set(\'showAddInitiativeModal\', false)'"></div>

            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="bg-slate-850 rounded-3xl overflow-hidden shadow-2xl border border-slate-750 max-w-2xl w-full relative z-10 p-6 sm:p-8 space-y-4 max-h-[90vh] flex flex-col">
                    <div class="flex justify-between items-center pb-3 border-b border-slate-750 shrink-0">
                        <div>
                            <span class="text-[9px] font-black text-blue-400 uppercase tracking-widest block font-display">Configure Initiative Settings</span>
                            <h3 class="text-base font-black text-white uppercase tracking-wider font-display">
                                {{ $showAddInitiativeModal ? 'Create Initiative' : 'Edit Initiative Info' }}
                            </h3>
                        </div>
                        <button type="button" wire:click="$set('showEditInfoModal', false); $set('showAddInitiativeModal', false);" 
                                class="text-slate-400 hover:text-slate-200 p-2 rounded-full hover:bg-slate-800 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="{{ $showAddInitiativeModal ? 'storeInitiative' : 'saveInfo' }}" class="flex flex-col flex-1 overflow-hidden space-y-5">
                        <div class="space-y-4 overflow-y-auto flex-1 pr-2 py-1">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Initiative Title</label>
                                <input type="text" wire:model="title" required class="w-full rounded-xl border border-slate-700 bg-slate-900 px-3 py-2.5 text-xs text-slate-100 outline-none focus:border-[#1e40af] transition">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Update Project Thumbnail</label>
                                @if($existingThumbnailUrl && !$thumbnail)
                                    <div class="mb-2">
                                        <img src="{{ $existingThumbnailUrl }}" class="w-24 h-16 object-cover rounded-xl border border-slate-700">
                                    </div>
                                @endif
                                <input type="file" wire:model="thumbnail" class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 transition">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Publishing Status</label>
                                    <select wire:model="is_coming_soon" class="w-full rounded-xl border border-slate-700 bg-slate-900 px-3 py-2.5 text-xs text-slate-100 outline-none transition">
                                        <option value="0">Published</option>
                                        <option value="1">Draft / Coming Soon</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Quick Form Access</label>
                                    <select wire:model="show_in_quick_forms" class="w-full rounded-xl border border-slate-700 bg-slate-900 px-3 py-2.5 text-xs text-slate-100 outline-none transition">
                                        <option value="1">Show in Quick Forms</option>
                                        <option value="0">Hide from Quick Forms</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Highlighted Program</label>
                                    <select wire:model="is_highlighted" class="w-full rounded-xl border border-slate-700 bg-slate-900 px-3 py-2.5 text-xs text-slate-100 outline-none transition">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                    @error('is_highlighted')
                                        <p class="text-rose-500 text-[10px] font-bold mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Form Route</label>
                                    <input type="text" wire:model="form_route" placeholder="e.g. forms.custom.create" class="w-full rounded-xl border border-slate-700 bg-slate-900 px-3 py-2.5 text-xs text-slate-100 outline-none focus:border-[#1e40af] transition">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Description</label>
                                <textarea wire:model="description" required rows="3" class="w-full rounded-xl border border-slate-700 bg-slate-900 px-3 py-2.5 text-xs text-slate-100 outline-none resize-none" placeholder="Provide a short overview..."></textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-750 shrink-0">
                            <button type="button" wire:click="$set('showEditInfoModal', false); $set('showAddInitiativeModal', false);" class="px-4 py-2.5 text-xs font-bold bg-slate-800 text-slate-300 border border-slate-700 hover:bg-slate-750 rounded-xl transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2.5 text-xs font-bold bg-[#1e40af] hover:bg-blue-700 text-white rounded-xl transition">
                                Save Details
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 2: Edit Builder Modal (Form Fields Configuration) -->
    @if($showEditBuilderModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" wire:click="$set('showEditBuilderModal', false)"></div>

            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="bg-slate-850 rounded-3xl overflow-hidden shadow-2xl border border-slate-750 max-w-4xl w-full relative z-10 p-6 sm:p-8 space-y-4 max-h-[90vh] flex flex-col">
                    <div class="flex justify-between items-center pb-3 border-b border-slate-750 shrink-0">
                        <div>
                            <span class="text-[9px] font-black text-blue-400 uppercase tracking-widest block font-display">Configure Custom Form Fields</span>
                            <h3 class="text-base font-black text-white uppercase tracking-wider font-display">Edit Dynamic Form Builder</h3>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button type="button" wire:click="addField" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-wider rounded-xl transition flex items-center space-x-1">
                                <span>➕ Add Field</span>
                            </button>
                            <button type="button" wire:click="$set('showEditBuilderModal', false)" class="text-slate-400 hover:text-slate-200 p-2 rounded-full hover:bg-slate-800 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    </div>

                    <form wire:submit.prevent="saveBuilder" class="flex flex-col flex-1 overflow-hidden space-y-5">
                        <div class="space-y-4 overflow-y-auto flex-1 pr-2 py-1 text-left">
                            @if(count($builderFields) === 0)
                                <div class="text-center py-10 border border-dashed border-slate-700 rounded-2xl bg-slate-900/40 text-xs text-slate-500 font-sans">
                                    No custom fields configured. Standard fields (First name, Last name, Email) will be used. Click "Add Field" to begin.
                                </div>
                            @endif

                            @foreach($builderFields as $index => $field)
                                <div class="p-4 bg-slate-900 border border-slate-800 rounded-2xl space-y-4 relative shadow-md">
                                    <button type="button" wire:click="removeField({{ $index }})" 
                                            class="absolute top-3 right-3 text-slate-500 hover:text-rose-500 p-1.5 rounded-lg hover:bg-rose-500/10 transition" 
                                            title="Remove Field">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pr-8">
                                        <div>
                                            <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Field Label (Visible to citizen)</label>
                                            <input type="text" wire:model="builderFields.{{ $index }}.label" required class="w-full rounded-xl border border-slate-700 bg-slate-850 px-3 py-2 text-xs text-slate-100 outline-none focus:border-[#1e40af] transition" placeholder="e.g. High School Attended">
                                            @error("builderFields.{$index}.label")
                                                <p class="text-rose-500 text-[10px] font-bold mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Field Key (Unique identifier)</label>
                                            <input type="text" wire:model="builderFields.{{ $index }}.name" class="w-full rounded-xl border border-slate-700 bg-slate-850 px-3 py-2 text-xs text-slate-100 font-mono outline-none focus:border-[#1e40af] transition" placeholder="e.g. high_school_attended">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Field Input Type</label>
                                            <select wire:model="builderFields.{{ $index }}.type" class="w-full rounded-xl border border-slate-700 bg-slate-850 px-3 py-2 text-xs text-slate-100 outline-none transition">
                                                <option value="text">Single Line Text</option>
                                                <option value="textarea">Multi-line Paragraph</option>
                                                <option value="number">Numeric Value</option>
                                                <option value="date">Date Selector</option>
                                                <option value="select">Dropdown Options</option>
                                                <option value="file">File Attachment</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1">Input Placeholder / Hint</label>
                                            <input type="text" wire:model="builderFields.{{ $index }}.placeholder" class="w-full rounded-xl border border-slate-700 bg-slate-850 px-3 py-2 text-xs text-slate-100 outline-none transition" placeholder="e.g. Enter school name...">
                                        </div>
                                        <div class="flex items-center pt-5">
                                            <label class="inline-flex items-center text-[10px] font-bold text-slate-400 uppercase tracking-wider cursor-pointer select-none">
                                                <input type="checkbox" wire:model="builderFields.{{ $index }}.required" class="rounded border-slate-700 text-[#1e40af] focus:ring-[#1e40af]/20 mr-2 w-4 h-4 bg-slate-850">
                                                Required Field
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Dropdown Options Builder (Rendered only if select type is chosen) -->
                                    @if(($field['type'] ?? '') === 'select')
                                        <div class="pt-3 border-t border-slate-800 space-y-2">
                                            <div class="flex justify-between items-center">
                                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Dropdown Options</span>
                                                <button type="button" wire:click="addSelectOption({{ $index }})" class="px-2 py-1 bg-slate-800 hover:bg-slate-750 text-slate-300 text-[8px] font-black uppercase tracking-wider rounded-lg transition">
                                                    ➕ Add Option
                                                </button>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                                                @foreach($field['options'] ?? [] as $optIndex => $option)
                                                    <div class="flex items-center space-x-1.5">
                                                        <input type="text" wire:model="builderFields.{{ $index }}.options.{{ $optIndex }}" required class="flex-1 rounded-xl border border-slate-700 bg-slate-850 px-3 py-1.5 text-xs text-slate-100 outline-none transition" placeholder="e.g. Option {{ $optIndex + 1 }}">
                                                        <button type="button" wire:click="removeSelectOption({{ $index }}, {{ $optIndex }})" class="p-1 text-slate-500 hover:text-rose-500 hover:bg-rose-500/10 rounded-lg transition">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-750 shrink-0">
                            <button type="button" wire:click="$set('showEditBuilderModal', false)" class="px-4 py-2.5 text-xs font-bold bg-slate-800 text-slate-300 border border-slate-700 hover:bg-slate-750 rounded-xl transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2.5 text-xs font-bold bg-[#1e40af] hover:bg-blue-700 text-white rounded-xl transition">
                                Save Builder Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 3: Password Confirmation Delete Modal (Dark-themed, Slate-800) -->
    @if($showDeleteConfirmModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity" wire:click="$set('showDeleteConfirmModal', false)"></div>

            <div class="flex min-h-screen items-center justify-center p-4">
                <div class="bg-slate-850 rounded-3xl overflow-hidden shadow-2xl border border-slate-750 max-w-md w-full relative z-10 p-6 space-y-4">
                    <div class="pb-2 border-b border-slate-750 flex items-center justify-between">
                        <h3 class="text-sm font-black text-rose-450 uppercase tracking-wider font-display">Confirm Deletion</h3>
                        <button type="button" wire:click="$set('showDeleteConfirmModal', false)" class="text-slate-500 hover:text-slate-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <p class="text-xs text-slate-350 leading-relaxed">
                        Are you sure you want to delete this {{ $deleteConfirmType }}? This action will move it to the bin. Please enter your password to confirm.
                    </p>

                    <form wire:submit.prevent="{{ $deleteConfirmType === 'committee' ? 'deleteCommittee' : 'deleteInitiative' }}" class="space-y-4">
                        <div>
                            <label class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Confirm Password</label>
                            <input type="password" wire:model="confirmPassword" required class="w-full rounded-xl border border-slate-700 bg-slate-900 px-3 py-2 text-xs text-slate-100 outline-none focus:border-rose-500 transition">
                            @error('confirmPassword')
                                <p class="text-rose-500 text-[10px] font-bold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-2 pt-2">
                            <button type="button" wire:click="$set('showDeleteConfirmModal', false)" class="px-4 py-2 bg-slate-800 text-slate-300 border border-slate-700 hover:bg-slate-750 rounded-xl text-xs font-bold transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition">
                                Confirm Delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
