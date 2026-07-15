<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 reveal-on-scroll">
    <div class="text-center mb-8">
        <span class="text-xs font-black tracking-widest text-[#1e40af] uppercase font-display">Featured Initiatives</span>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-800 font-display mt-1.5 uppercase">Highlighted Programs</h1>
        <p class="text-xs text-slate-400 mt-2 max-w-sm mx-auto">Explore our featured programs directly managed by our youth representatives.</p>
    </div>

    @if($highlightedInitiatives->isEmpty())
        <div class="text-center py-12 border border-dashed border-slate-200 rounded-3xl bg-slate-50/50 text-xs text-slate-400">
            No highlighted programs configured at this moment.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-{{ min(3, $highlightedInitiatives->count()) }} gap-6 justify-center">
            @foreach($highlightedInitiatives as $qi)
                @php
                    $color = match($qi->committee_id) {
                        1 => 'text-indigo-600',
                        2 => 'text-emerald-600',
                        3 => 'text-amber-600',
                        4 => 'text-blue-600',
                        default => 'text-[#1e40af]'
                    };
                    $bgColor = match($qi->committee_id) {
                        1 => 'bg-indigo-50',
                        2 => 'bg-emerald-50',
                        3 => 'bg-amber-50',
                        4 => 'bg-blue-50',
                        default => 'bg-blue-50/50'
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
                    $btnText = match($qi->form_route) {
                        'forms.health.create' => 'Book Consultation',
                        'forms.mental-health.create' => 'Get Support',
                        'forms.silid.create' => 'Book Library Slot',
                        'forms.medicine.create' => 'Apply for Medicine',
                        'forms.sports.create' => 'Register for Sports',
                        default => 'Apply Now'
                    };
                @endphp
                <div class="card flex flex-col justify-between h-full hover:-translate-y-1 hover:shadow-md transition">
                    <div class="space-y-3">
                        <div class="w-10 h-10 rounded-lg {{ $bgColor }} {{ $color }} flex items-center justify-center">
                            <x-category-icon name="{{ $icon }}" class="w-5 h-5 {{ $color }}" />
                        </div>
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wide font-display">{{ $qi->title }}</h3>
                        <p class="text-xs text-slate-500 leading-relaxed font-sans text-justify">
                            {{ $qi->description }}
                        </p>
                    </div>
                    <div class="pt-5 border-t border-slate-100 mt-5">
                        <a href="{{ $qi->form_route ? route($qi->form_route) : route('forms.custom.create', $qi->id) }}" 
                           @if($formName) 
                               @click.prevent="openForm('{{ $formName }}')" 
                           @else 
                               @click.prevent="openCustomForm({{ json_encode($qi->only(['id', 'title', 'description', 'custom_fields'])) }})" 
                           @endif
                           class="btn-primary w-full text-center block">
                            {{ $btnText }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>
