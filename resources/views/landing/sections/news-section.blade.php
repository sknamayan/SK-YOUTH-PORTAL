<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 reveal-on-scroll">
    <div class="text-center mb-8">
        <span class="text-xs font-black tracking-widest text-[#1e40af] uppercase font-display">Namayan Feed</span>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-800 font-display mt-1.5 uppercase">News Articles</h1>
        <p class="text-xs text-slate-400 mt-2 max-w-sm mx-auto">Stay updated with the latest community reports, athletic achievements, and local stories.</p>
    </div>

    @if(!$featuredArticle && $recentArticles->isEmpty())
        <div class="text-center py-12 border border-dashed border-slate-200 rounded-3xl bg-slate-50/50 text-xs text-slate-400">
            No news articles posted yet.
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            @if($featuredArticle)
                <div class="lg:col-span-7 flex flex-col group">
                    <a href="{{ route('news.show', $featuredArticle->slug) }}" class="block overflow-hidden rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition duration-300 relative aspect-video bg-slate-50">
                        @if($featuredArticle->image_path)
                            @if(str_starts_with($featuredArticle->image_path, 'http'))
                                <img src="{{ $featuredArticle->image_path }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $featuredArticle->title }}">
                            @else
                                <img src="{{ asset('storage/' . $featuredArticle->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $featuredArticle->title }}">
                            @endif
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="text-slate-350 text-3xl">📷</span>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/20 to-transparent"></div>
                        <div class="absolute bottom-5 left-5 right-5 space-y-2">
                            <span class="bg-blue-600 text-white text-[9px] font-black uppercase tracking-wider px-2.5 py-1 rounded-md">{{ $featuredArticle->category }}</span>
                            <span class="text-white/80 text-[10px] font-bold uppercase tracking-wider ml-2">{{ $featuredArticle->read_time }} Min Read</span>
                        </div>
                    </a>
                    <div class="mt-4 space-y-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            {{ $featuredArticle->published_at ? \Carbon\Carbon::parse($featuredArticle->published_at)->format('M d, Y') : $featuredArticle->created_at->format('M d, Y') }}
                        </span>
                        <h2 class="text-lg sm:text-xl font-black text-slate-800 leading-snug tracking-tight font-display hover:text-[#1e40af] transition uppercase">
                            <a href="{{ route('news.show', $featuredArticle->slug) }}">{{ $featuredArticle->title }}</a>
                        </h2>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            {{ $featuredArticle->excerpt }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="lg:col-span-5 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-xs font-black tracking-wider text-slate-400 uppercase font-display">Recent Stories</h3>
                </div>
                <div class="space-y-4">
                    @forelse($recentArticles as $recent)
                        <div class="flex items-start space-x-4 group">
                            <a href="{{ route('news.show', $recent->slug) }}" class="w-24 h-16 sm:w-28 sm:h-20 rounded-2xl overflow-hidden border border-slate-100 shrink-0 block relative bg-slate-50">
                                @if($recent->image_path)
                                    @if(str_starts_with($recent->image_path, 'http'))
                                        <img src="{{ $recent->image_path }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $recent->title }}">
                                    @else
                                        <img src="{{ asset('storage/' . $recent->image_path) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="{{ $recent->title }}">
                                    @endif
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="text-slate-350 text-lg">📷</span>
                                    </div>
                                @endif
                            </a>
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="text-[9px] font-extrabold text-[#1e40af] uppercase tracking-wider">{{ $recent->category }}</span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">•</span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $recent->read_time }} Min Read</span>
                                </div>
                                <h4 class="text-xs font-extrabold text-slate-800 hover:text-[#1e40af] transition font-display uppercase leading-tight line-clamp-2">
                                    <a href="{{ route('news.show', $recent->slug) }}">{{ $recent->title }}</a>
                                </h4>
                                <p class="text-[10px] text-slate-450 leading-relaxed line-clamp-2">
                                    {{ $recent->excerpt }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs py-4">No recent articles found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</section>
