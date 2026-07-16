@extends('layouts.app')

@section('content')
<div x-data="sportsFormBuilder()" class="flex-1 flex flex-col md:flex-row bg-[#f8fafc] dark:bg-slate-955">

    @include('layouts.dashboard-sidebar')

    <div class="flex-1 flex flex-col min-w-0">
        <div class="p-6 md:p-8 space-y-6 flex-1 overflow-y-auto">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider mb-2">
                        <a href="{{ route('admin.sports-league.index') }}" class="text-slate-400 hover:text-[#1e40af] dark:hover:text-blue-400">SIKLAB</a>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-800 dark:text-slate-100">SIKLAB Form Builder</span>
                    </div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-800 dark:text-white font-display uppercase">Dedicated SIKLAB Registration Form Builder</h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Design isolated registration forms specifically tailored for SIKLAB tournaments, divisions, and team/player lists.</p>
                </div>
                <a href="{{ route('admin.sports-league.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-900 transition">Back to League</a>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                <!-- Field palette -->
                <div class="xl:col-span-3 space-y-4">
                    <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-5 shadow-sm">
                        <h2 class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-4">Sports Field Types</h2>
                        <div class="space-y-2">
                            <template x-for="palette in palettes" :key="palette.type">
                                <button type="button" @click="addField(palette.type)"
                                    class="w-full flex items-center gap-3 px-4 py-3 rounded-2xl border border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 hover:border-emerald-500/30 hover:bg-emerald-50/50 dark:hover:bg-emerald-955/20 transition text-left">
                                    <span class="w-8 h-8 rounded-xl bg-emerald-500/10 dark:bg-emerald-955/40 text-emerald-600 dark:text-emerald-300 flex items-center justify-center text-xs font-black" x-text="palette.icon"></span>
                                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200" x-text="palette.label"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Canvas -->
                <div class="xl:col-span-5">
                    <form method="POST" action="{{ route('admin.sports-league.form-builder.store') }}" @submit="prepareSubmit">
                        @csrf

                        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-5 shadow-sm min-h-[28rem] space-y-5">
                            
                            <!-- Sports League Metadata Fields -->
                            <div class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-850 rounded-2xl space-y-3">
                                <h3 class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Division Details</h3>
                                
                                <div>
                                    <label class="text-[9px] font-bold uppercase text-slate-400 mb-1 block">League Name</label>
                                    <input type="text" name="league_name" required placeholder="e.g. SK Namayan Summer League 2026" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-xs dark:text-white">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-[9px] font-bold uppercase text-slate-400 mb-1 block">Sport Type</label>
                                        <select name="sport" required class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-xs dark:text-white">
                                            <option value="Basketball">Basketball</option>
                                            <option value="Volleyball">Volleyball</option>
                                            <option value="Badminton">Badminton</option>
                                            <option value="Other">Other Sport</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-bold uppercase text-slate-400 mb-1 block">Division Name</label>
                                        <input type="text" name="division_name" required placeholder="e.g. Basketball Senior" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-xs dark:text-white">
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[9px] font-bold uppercase text-slate-400 mb-1 block">Description</label>
                                    <textarea name="description" placeholder="Short description/rules for this division registration..." rows="2" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-xs dark:text-white resize-none"></textarea>
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <h2 class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest font-mono">Form Fields Schema</h2>
                                <span class="text-[10px] font-bold text-slate-400 uppercase" x-text="fields.length + ' fields'"></span>
                            </div>

                            <div class="space-y-3" @dragover.prevent @drop.prevent="dropOnCanvas($event)">
                                <template x-if="fields.length === 0">
                                    <div class="text-center py-16 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl">
                                        <p class="text-sm text-slate-400">Add fields from the palette or drag them here.</p>
                                    </div>
                                </template>

                                <template x-for="(field, index) in fields" :key="field._uid">
                                    <div draggable="true"
                                         @dragstart="dragStart(index, $event)"
                                         @dragover.prevent="dragOver(index)"
                                         @drop.prevent="drop(index)"
                                         :class="dragOverIndex === index ? 'ring-2 ring-emerald-500/40' : ''"
                                         class="rounded-2xl border border-slate-100 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 p-4 cursor-grab active:cursor-grabbing transition">

                                         <div class="flex items-start justify-between gap-3 mb-3">
                                             <div class="flex items-center gap-2">
                                                 <span class="text-slate-300 dark:text-slate-600 font-mono">⠿</span>
                                                 <span class="text-[10px] font-black uppercase tracking-wider text-emerald-655 dark:text-emerald-400" x-text="field.type"></span>
                                             </div>
                                             <button type="button" @click="removeField(index)" class="text-rose-500 hover:text-rose-700 text-xs font-bold uppercase">Remove</button>
                                         </div>

                                         <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                             <div>
                                                 <label class="text-[9px] font-bold uppercase text-slate-400 mb-1 block">Label</label>
                                                 <input type="text" x-model="field.label" @input="syncName(field)" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-950 px-3 py-2 text-sm dark:text-white">
                                                 <input type="hidden" :name="`custom_fields[${index}][label]`" :value="field.label">
                                             </div>
                                             <div>
                                                 <label class="text-[9px] font-bold uppercase text-slate-400 mb-1 block">Field Name (Internal)</label>
                                                 <input type="text" x-model="field.name" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-955 px-3 py-2 text-sm font-mono dark:text-white">
                                                 <input type="hidden" :name="`custom_fields[${index}][name]`" :value="field.name">
                                             </div>
                                             <div>
                                                 <label class="text-[9px] font-bold uppercase text-slate-400 mb-1 block">Field Type</label>
                                                 <select x-model="field.type" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-955 px-3 py-2 text-sm dark:text-white">
                                                     <option value="text">Text Input</option>
                                                     <option value="textarea">Text Area</option>
                                                     <option value="number">Number</option>
                                                     <option value="date">Date Picker</option>
                                                     <option value="select">Dropdown Select</option>
                                                     <option value="radio">Radio Buttons</option>
                                                     <option value="checkbox">Checkboxes</option>
                                                     <option value="file">File Upload (IDs)</option>
                                                 </select>
                                                 <input type="hidden" :name="`custom_fields[${index}][type]`" :value="field.type">
                                             </div>
                                             <div>
                                                 <label class="text-[9px] font-bold uppercase text-slate-400 mb-1 block">Placeholder</label>
                                                 <input type="text" x-model="field.placeholder" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-955 px-3 py-2 text-sm dark:text-white">
                                                 <input type="hidden" :name="`custom_fields[${index}][placeholder]`" :value="field.placeholder">
                                             </div>
                                         </div>

                                         <!-- Multi-options Textarea Configuration -->
                                         <div x-show="['select', 'radio', 'checkbox'].includes(field.type)" class="mt-3">
                                             <label class="text-[9px] font-bold uppercase text-slate-400 mb-1 block">
                                                 Options List (one per line)
                                             </label>
                                             <textarea x-model="field.optionsText" @input="syncOptions(field)" rows="3" class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-955 px-3 py-2 text-sm dark:text-white" placeholder="Option 1&#10;Option 2"></textarea>
                                             <template x-for="(opt, oi) in field.options" :key="oi">
                                                 <input type="hidden" :name="`custom_fields[${index}][options][${oi}]`" :value="opt">
                                             </template>
                                         </div>

                                         <label class="inline-flex items-center gap-2 mt-3 text-xs font-bold text-slate-600 dark:text-slate-300">
                                             <input type="checkbox" x-model="field.required" class="rounded border-slate-300 text-emerald-600">
                                             Required field
                                             <input type="hidden" :name="`custom_fields[${index}][required]`" :value="field.required ? 1 : 0">
                                         </label>
                                     </div>
                                 </template>
                             </div>

                             <div class="mt-6 flex justify-end">
                                 <button type="submit" class="inline-flex items-center px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-black uppercase tracking-wider transition active:scale-95">
                                     Save Form Schema
                                 </button>
                             </div>
                         </div>
                     </form>
                 </div>

                 <!-- Dynamic Live Preview Sidebar (Alpine.js integration) -->
                 <div class="xl:col-span-4">
                     <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-5 shadow-sm sticky top-6">
                         <h2 class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-4">Sports Live Preview</h2>
                         <div class="space-y-4">
                             <template x-for="field in fields" :key="'preview-' + field._uid">
                                 <div>
                                     <label class="block text-[10px] font-bold uppercase text-slate-500 dark:text-slate-400 mb-1.5">
                                         <span x-text="field.label || 'Untitled Field'"></span>
                                         <span x-show="field.required" class="text-rose-500">*</span>
                                     </label>
                                     
                                     <template x-if="field.type === 'textarea'">
                                         <textarea disabled :placeholder="field.placeholder" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-955 px-3 py-2 text-xs"></textarea>
                                     </template>
                                     
                                     <template x-if="field.type === 'select'">
                                         <select disabled class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-955 px-3 py-2 text-xs">
                                             <option x-text="field.placeholder || 'Select an option'"></option>
                                             <template x-for="opt in field.options" :key="opt">
                                                 <option x-text="opt"></option>
                                             </template>
                                         </select>
                                     </template>

                                     <template x-if="field.type === 'radio'">
                                         <div class="space-y-1 mt-1">
                                             <template x-for="opt in field.options" :key="opt">
                                                 <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400 select-none">
                                                     <input type="radio" disabled class="rounded-full border-slate-300 text-emerald-600">
                                                     <span x-text="opt"></span>
                                                 </label>
                                             </template>
                                         </div>
                                     </template>

                                     <template x-if="field.type === 'checkbox'">
                                         <div class="space-y-1 mt-1">
                                             <template x-for="opt in field.options" :key="opt">
                                                 <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400 select-none">
                                                     <input type="checkbox" disabled class="rounded border-slate-300 text-emerald-600">
                                                     <span x-text="opt"></span>
                                                 </label>
                                             </template>
                                         </div>
                                     </template>
                                     
                                     <template x-if="field.type === 'file'">
                                         <div class="w-full rounded-xl border border-dashed border-slate-350 dark:border-slate-700 bg-slate-50 dark:bg-slate-955 px-3 py-4 text-center text-xs text-slate-450">
                                             📎 Click to drop file upload
                                         </div>
                                     </template>
                                     
                                     <template x-if="!['textarea','select','radio','checkbox','file'].includes(field.type)">
                                         <input disabled :type="field.type === 'number' ? 'number' : (field.type === 'date' ? 'date' : 'text')" :placeholder="field.placeholder" class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-955 px-3 py-2 text-xs">
                                     </template>
                                 </div>
                             </template>
                             <p x-show="fields.length === 0" class="text-sm text-slate-400 text-center py-8">Your fields live preview will load here.</p>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('sportsFormBuilder', (initialFields = []) => ({
        fields: initialFields.map(f => ({
            ...f,
            _uid: crypto.randomUUID(),
            options: f.options || [],
            optionsText: (f.options || []).join('\n'),
            required: !!f.required,
        })),
        dragIndex: null,
        dragOverIndex: null,
        palettes: [
            { type: 'text', label: 'Player Name / Text', icon: 'T' },
            { type: 'textarea', label: 'Team Details Area', icon: '¶' },
            { type: 'select', label: 'Jersey Size Dropdown', icon: '▾' },
            { type: 'radio', label: 'Position Radio', icon: '⊙' },
            { type: 'checkbox', label: 'Division Select Box', icon: '☑' },
            { type: 'number', label: 'Jersey Number', icon: '#' },
            { type: 'date', label: 'Birth Date Picker', icon: '📅' },
            { type: 'file', label: 'Medical Clearance Upload', icon: '📎' },
        ],
        addField(type) {
            this.fields.push({
                _uid: crypto.randomUUID(),
                label: '',
                name: '',
                type,
                placeholder: '',
                required: false,
                options: [],
                optionsText: '',
            });
        },
        removeField(index) {
            this.fields.splice(index, 1);
        },
        syncName(field) {
            if (!field.name && field.label) {
                field.name = field.label.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
            }
        },
        syncOptions(field) {
            field.options = field.optionsText.split('\n').map(o => o.trim()).filter(Boolean);
        },
        dragStart(index, event) {
            this.dragIndex = index;
            event.dataTransfer.effectAllowed = 'move';
        },
        dragOver(index) {
            this.dragOverIndex = index;
        },
        drop(index) {
            if (this.dragIndex === null || this.dragIndex === index) return;
            const item = this.fields.splice(this.dragIndex, 1)[0];
            this.fields.splice(index, 0, item);
            this.dragIndex = null;
            this.dragOverIndex = null;
        },
        dropOnCanvas() {
            this.dragOverIndex = null;
        },
        prepareSubmit() {
            this.fields.forEach(f => this.syncOptions(f));
        },
    }));
});
</script>
@endsection
