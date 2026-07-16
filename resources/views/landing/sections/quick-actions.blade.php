@php
    $quickInitiatives = \App\Models\Initiative::where('show_in_quick_forms', true)->get();
@endphp
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 reveal-on-scroll">
    <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 grid grid-cols-2 sm:flex sm:flex-wrap sm:items-center sm:justify-center gap-3">
        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider px-3 col-span-2 text-center sm:col-span-auto sm:text-left w-full sm:w-auto mb-1 sm:mb-0">Quick Forms:</span>

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
               class="btn-outline btn-sm space-x-1.5 shadow-sm justify-center w-full sm:w-auto">
                <x-category-icon name="{{ $icon }}" class="w-4 h-4 {{ $color }}" />
                <span>{{ $qi->title }}</span>
            </a>
        @endforeach

        <!-- Sports League (Kept) -->
        <a href="{{ route('forms.sports.create') }}"
           class="btn-outline btn-sm space-x-1.5 shadow-sm justify-center w-full sm:w-auto">
            <x-category-icon name="sports" class="w-4 h-4 text-blue-600" />
            <span>SIKLAB</span>
        </a>

        <!-- Track Request (Kept) -->
        <a href="{{ route('track.index') }}" class="btn-primary btn-sm space-x-1.5 shadow-sm justify-center col-span-2 sm:col-span-auto w-full sm:w-auto">
            <x-category-icon name="track" class="w-4 h-4" />
            <span>Track Request</span>
        </a>
    </div>
</section>
