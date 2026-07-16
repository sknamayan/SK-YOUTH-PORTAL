@php
    $quickInitiatives = \App\Models\Initiative::where('show_in_quick_forms', true)->get();
@endphp
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 reveal-on-scroll my-12">
    <!-- Section Header (Matches Our Services design) -->
    <div class="text-center mb-8">
        <span class="text-xs font-black tracking-widest text-[#1e40af] uppercase font-display">QUICK FORMS & APPLICATIONS</span>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-800 font-display mt-1.5 uppercase">Request Online</h1>
        <p class="text-xs text-slate-400 mt-2 max-w-md mx-auto">Access our services instantly. Select a quick form below to submit a service request, book a facility, or register for sports league.</p>
    </div>

    <!-- Grid Container (2 columns on mobile) -->
    <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 grid grid-cols-2 lg:grid-cols-3 gap-3">
        <!-- Dynamic Quick Forms -->
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
               class="bg-white border border-slate-100 hover:border-[#1e40af] text-slate-700 hover:text-[#1e40af] hover:-translate-y-0.5 hover:shadow-sm transition-all duration-200 rounded-xl p-2 sm:p-4 flex items-center space-x-2 sm:space-x-3 w-full justify-start active:scale-98 overflow-hidden">
                <div class="p-1 sm:p-2 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <x-category-icon name="{{ $icon }}" class="w-4 h-4 sm:w-5 h-5 {{ $color }}" />
                </div>
                <span class="font-extrabold text-[10px] sm:text-xs tracking-wider uppercase font-display whitespace-nowrap overflow-hidden text-ellipsis">{{ $qi->title }}</span>
            </a>
        @endforeach

        <!-- Sports League (Kept) -->
        <a href="{{ route('forms.sports.create') }}"
           class="bg-white border border-slate-100 hover:border-[#1e40af] text-slate-700 hover:text-[#1e40af] hover:-translate-y-0.5 hover:shadow-sm transition-all duration-200 rounded-xl p-2 sm:p-4 flex items-center space-x-2 sm:space-x-3 w-full justify-start active:scale-98 overflow-hidden">
            <div class="p-1 sm:p-2 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <x-category-icon name="sports" class="w-4 h-4 sm:w-5 h-5 text-blue-600" />
            </div>
            <span class="font-extrabold text-[10px] sm:text-xs tracking-wider uppercase font-display whitespace-nowrap overflow-hidden text-ellipsis">SIKLAB</span>
        </a>

        <!-- Track Request (Kept) -->
        <a href="{{ route('track.index') }}" class="bg-[#1e40af] text-white hover:bg-blue-700 hover:-translate-y-0.5 hover:shadow-md transition-all duration-200 rounded-xl p-2.5 sm:p-4 flex items-center justify-center space-x-2 sm:space-x-3 w-full col-span-2 lg:col-span-3 active:scale-98">
            <x-category-icon name="track" class="w-4 h-4 sm:w-5 h-5 text-white" />
            <span class="font-black text-[10px] sm:text-xs tracking-widest uppercase font-display whitespace-nowrap">Track Your Request</span>
        </a>
    </div>
</section>
