<section class="max-w-3xl mx-auto px-4 sm:px-6 reveal-on-scroll">
    <div class="text-center mb-8">
        <span class="text-xs font-black tracking-widest text-[#1e40af] uppercase font-display">Latest Broadcasts</span>
        <h1 class="text-2xl font-black tracking-tight text-slate-800 font-display mt-1.5 uppercase">Announcements</h1>
        <p class="text-xs text-slate-400 mt-2">Check active policy broadcasts, eligibility definitions, and public updates below.</p>
    </div>

    @if($announcements->isEmpty())
        <div class="text-center py-8 text-slate-400 text-xs bg-slate-50 border border-slate-100 rounded-2xl">
            No active announcements at the moment. Check back later!
        </div>
    @else
        <div x-data="{ activeAccordion: 0 }" class="space-y-3">
            @foreach($announcements as $index => $ann)
                @php
                    $headerClass = match($ann->type) {
                        'success' => 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border-emerald-100',
                        'warning' => 'bg-amber-50 text-amber-800 hover:bg-amber-100 border-amber-100',
                        'info' => 'bg-blue-50 text-blue-800 hover:bg-blue-100 border-blue-100',
                        default => 'bg-slate-50 text-slate-800 hover:bg-slate-100 border-slate-100'
                    };
                    $bodyClass = match($ann->type) {
                        'success' => 'bg-emerald-50/20 border-emerald-100/50 text-slate-700',
                        'warning' => 'bg-amber-50/20 border-amber-100/50 text-slate-700',
                        'info' => 'bg-blue-50/20 border-blue-100/50 text-slate-700',
                        default => 'bg-slate-50/20 border-slate-100/50 text-slate-700'
                    };
                @endphp

                <div class="border rounded-2xl overflow-hidden transition duration-200">
                    <button @click="activeAccordion = (activeAccordion === {{ $index }} ? null : {{ $index }})"
                            class="w-full text-left px-5 py-4 font-bold text-xs tracking-wider uppercase flex items-center justify-between transition {{ $headerClass }}">
                        <span class="font-display">{{ $ann->title }}</span>
                        <svg class="w-4 h-4 transform transition-transform duration-200"
                             :class="activeAccordion === {{ $index }} ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="activeAccordion === {{ $index }}"
                         x-collapse
                         class="p-5 text-xs leading-relaxed border-t {{ $bodyClass }}">
                        <p class="mb-3">{{ $ann->body }}</p>
                        <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider block">Published: {{ $ann->published_at->format('M d, Y h:i A') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>
