@php
    $quickInitiatives = \App\Models\Initiative::where('show_in_quick_forms', true)->get();
@endphp
<section class="block sm:hidden max-w-7xl mx-auto px-4 reveal-on-scroll my-6">
    <!-- Header styled like Our Services (Mobile Only) -->
    <div class="text-center mb-6">
        <span class="text-xs font-black tracking-widest text-[#1e40af] uppercase font-display">QUICK FORMS & APPLICATIONS</span>
        <h1 class="text-xl font-black tracking-tight text-slate-800 font-display mt-1 uppercase">Request Online</h1>
        <p class="text-[10px] text-slate-400 mt-1 max-w-xs mx-auto">Select a quick form below to submit a request, book a facility, or register for SIKLAB.</p>
    </div>

    <!-- 2-Column Grid Cards (Mobile Only) -->
    <div class="bg-slate-50 dark:bg-slate-50 border border-slate-100 dark:border-slate-100 rounded-2xl p-4 grid grid-cols-2 gap-3">
        @foreach($quickInitiatives as $qi)
            @php
                $color = match($qi->committee_id) {
                    1 => 'text-indigo-600',
                    2 => 'text-emerald-600',
                    3 => 'text-amber-600',
                    4 => 'text-blue-600',
                    default => 'text-[#1e40af]'
                };
                $icon = match($qi->committee_id) {
                    1 => 'education',
                    2 => 'health',
                    3 => 'medicine',
                    4 => 'sports',
                    default => 'logs'
                };
                $formName = match($qi->form_route) {
                    'forms.health.create' => 'health',
                    'forms.mental-health.create' => 'mental-health',
                    'forms.silid.create' => 'silid',
                    'forms.medicine.create' => 'medicine',
                    default => null
                };
            @endphp
            <a href="{{ $qi->form_route ? route($qi->form_route) : route('forms.custom.create', $qi->id) }}"
               @if($formName)
                   @click.prevent="openForm('{{ $formName }}')"
               @else
                   @click.prevent="openCustomForm({{ json_encode($qi->only(['id', 'title', 'description', 'custom_fields'])) }})"
               @endif
               class="bg-white dark:bg-white border border-slate-100 dark:border-slate-100 hover:border-[#1e40af] text-slate-700 dark:text-slate-700 hover:text-[#1e40af] hover:-translate-y-0.5 hover:shadow-sm transition-all duration-200 rounded-xl p-2.5 flex items-center gap-2 w-full justify-start active:scale-98 overflow-hidden min-w-0">
                <div class="p-1 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <x-category-icon name="{{ $icon }}" class="w-4 h-4 {{ $color }}" />
                </div>
                <span class="font-extrabold text-[10px] tracking-wider uppercase font-display whitespace-nowrap overflow-hidden text-ellipsis min-w-0 flex-1">{{ $qi->title }}</span>
            </a>
        @endforeach

        <!-- Sports League -->
        <a href="{{ route('forms.sports.create') }}"
           class="bg-white dark:bg-white border border-slate-100 dark:border-slate-100 hover:border-[#1e40af] text-slate-700 dark:text-slate-700 hover:text-[#1e40af] hover:-translate-y-0.5 hover:shadow-sm transition-all duration-200 rounded-xl p-2.5 flex items-center gap-2 w-full justify-start active:scale-98 overflow-hidden min-w-0">
            <div class="p-1 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <x-category-icon name="sports" class="w-4 h-4 text-blue-600" />
            </div>
            <span class="font-extrabold text-[10px] tracking-wider uppercase font-display whitespace-nowrap overflow-hidden text-ellipsis min-w-0 flex-1">SIKLAB</span>
        </a>

        <!-- Track Request -->
        <a href="{{ route('track.index') }}"
           class="bg-[#1e40af] text-white hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200 rounded-xl p-2.5 flex items-center justify-center gap-2 w-full col-span-2 active:scale-98 min-w-0">
            <x-category-icon name="track" class="w-4 h-4 text-white shrink-0" />
            <span class="font-black text-[10px] tracking-widest uppercase font-display whitespace-nowrap overflow-hidden text-ellipsis min-w-0">Track Your Request</span>
        </a>
    </div>
</section>

<!-- ================= DESKTOP/TABLET VIEW ONLY ================= -->
<section class="hidden sm:block max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 reveal-on-scroll">
    <div class="bg-slate-50 dark:bg-slate-50 border border-slate-100 dark:border-slate-100 rounded-2xl p-4 flex flex-wrap items-center justify-center gap-3">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider px-3">Quick Forms:</span>

        @foreach($quickInitiatives as $qi)
            @php
                $color = match($qi->committee_id) {
                    1 => 'text-indigo-600',
                    2 => 'text-emerald-600',
                    3 => 'text-amber-600',
                    4 => 'text-blue-600',
                    default => 'text-[#1e40af]'
                };
                $icon = match($qi->committee_id) {
                    1 => 'education',
                    2 => 'health',
                    3 => 'medicine',
                    4 => 'sports',
                    default => 'logs'
                };
                $formName = match($qi->form_route) {
                    'forms.health.create' => 'health',
                    'forms.mental-health.create' => 'mental-health',
                    'forms.silid.create' => 'silid',
                    'forms.medicine.create' => 'medicine',
                    default => null
                };
            @endphp
            <a href="{{ $qi->form_route ? route($qi->form_route) : route('forms.custom.create', $qi->id) }}"
               @if($formName)
                   @click.prevent="openForm('{{ $formName }}')"
               @else
                   @click.prevent="openCustomForm({{ json_encode($qi->only(['id', 'title', 'description', 'custom_fields'])) }})"
               @endif
               class="btn-outline btn-sm space-x-1.5 shadow-sm">
                <x-category-icon name="{{ $icon }}" class="w-4 h-4 {{ $color }}" />
                <span>{{ $qi->title }}</span>
            </a>
        @endforeach

        <a href="{{ route('forms.sports.create') }}" class="btn-outline btn-sm space-x-1.5 shadow-sm">
            <x-category-icon name="sports" class="w-4 h-4 text-blue-600" />
            <span>SIKLAB</span>
        </a>

        <a href="{{ route('track.index') }}" class="btn-primary btn-sm space-x-1.5 shadow-sm">
            <x-category-icon name="track" class="w-4 h-4" />
            <span>Track Request</span>
        </a>
    </div>
</section>
