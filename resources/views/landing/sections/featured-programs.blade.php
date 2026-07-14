<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 reveal-on-scroll">
    <div class="text-center mb-8">
        <span class="text-xs font-black tracking-widest text-[#1e40af] uppercase font-display">Interactive Catalog</span>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-800 font-display mt-1.5 uppercase">Our Services</h1>
        <p class="text-xs text-slate-400 mt-2 max-w-md mx-auto">Select a project area below to see subtopics, check schedules, or apply for service assistance in Barangay Namayan.</p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        @foreach($categories as $key => $cat)
            <a href="{{ route('projects.committee', ['project_slug' => 'sk-namayan-youth-services', 'committee_slug' => $key]) }}"
               class="h-32 bg-white border border-slate-100 hover:border-[#1e40af] text-slate-700 hover:text-[#1e40af] hover:-translate-y-1 hover:shadow-md transition-all duration-300 rounded-2xl flex flex-col justify-center items-center text-center p-5 group relative overflow-hidden active:scale-95">

                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-100 group-hover:scale-105 transition duration-200 mb-3">
                    <x-category-icon name="{{ $key }}" class="w-6 h-6 text-blue-600" />
                </div>

                <span class="font-extrabold text-[10px] sm:text-xs tracking-wider uppercase font-display leading-tight">{{ $cat['label'] }}</span>
            </a>
        @endforeach
    </div>
</section>
