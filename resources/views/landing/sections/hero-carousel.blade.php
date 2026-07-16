<section x-data="{
    activeSlide: 0,
    slides: {{ json_encode($formattedSlides) }},
    next() { this.activeSlide = (this.activeSlide + 1) % this.slides.length },
    prev() { this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length },
    init() { setInterval(() => this.next(), 5000) }
}" class="relative w-full max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 pt-3 sm:pt-6 reveal-on-scroll">
    <div class="relative h-[280px] sm:h-[360px] md:h-[450px] rounded-2xl sm:rounded-3xl overflow-hidden shadow-lg border border-slate-100 bg-slate-900 carousel-shadow">

        <template x-for="(slide, index) in slides" :key="index">
            <div x-show="activeSlide === index"
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-500"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute inset-0 bg-cover bg-center flex flex-col justify-end text-white p-4 pb-10 sm:p-8 sm:pb-12 md:p-16"
                 :style="`background-image: linear-gradient(to top, rgba(15, 23, 42, 0.95), rgba(15, 23, 42, 0.4)), url(${slide.image})`">

                <div class="relative z-10 max-w-2xl space-y-3 sm:space-y-4">
                    <span class="bg-blue-600/35 border border-blue-400/20 text-blue-200 text-[9px] font-black uppercase tracking-[0.25em] px-3 py-1 rounded-full backdrop-blur-md">Sangguniang Kabataan ng Namayan</span>
                    <h2 class="text-xl sm:text-4xl md:text-5xl font-extrabold tracking-tight font-display text-white leading-tight" x-text="slide.title"></h2>
                    <p class="text-slate-300 text-[11px] sm:text-sm md:text-base max-w-xl font-medium leading-relaxed" x-text="slide.desc"></p>
                    <div class="flex flex-col gap-2 pt-1 sm:pt-2 w-full sm:max-w-xs">
                        <!-- Custom CTA Button: only shown if url1 is a valid custom link -->
                        <template x-if="slide.url1 && slide.url1 !== '#'">
                            <a :href="slide.url1" @click.prevent="handleCtaClick(slide.url1)" class="btn-primary w-full justify-center" x-text="slide.cta1"></a>
                        </template>

                        <!-- Default Actions: shown horizontally only if url1 is '#' or empty -->
                        <template x-if="!slide.url1 || slide.url1 === '#'">
                            <div class="flex flex-row gap-2 w-full">
                                <a :href="slide.url1" @click.prevent="handleCtaClick(slide.url1)" class="btn-primary flex-1 justify-center text-center" x-text="slide.cta1 || 'Apply Now'"></a>
                                <a href="{{ route('track.index') }}" class="btn-outline text-white hover:text-[#1e40af] border-white/20 hover:bg-white flex-1 justify-center text-center">
                                    Track Request
                                </a>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        <button @click="prev()" class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-white/10 hover:bg-white/25 border border-white/10 backdrop-blur-md text-white hidden sm:flex items-center justify-center transition active:scale-95 group">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 group-hover:-translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button @click="next()" class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-white/10 hover:bg-white/25 border border-white/10 backdrop-blur-md text-white hidden sm:flex items-center justify-center transition active:scale-95 group">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
        </button>

        <div class="absolute bottom-3 sm:bottom-5 left-1/2 -translate-x-1/2 flex space-x-2.5">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="activeSlide = index"
                        class="w-2 h-2 sm:w-2.5 sm:h-2.5 rounded-full transition-all duration-300"
                        :class="activeSlide === index ? 'bg-white w-4 sm:w-6 shadow-sm' : 'bg-white/30 hover:bg-white/55'"></button>
            </template>
        </div>
    </div>
</section>
