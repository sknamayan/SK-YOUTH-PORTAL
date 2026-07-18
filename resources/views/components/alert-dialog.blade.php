<div x-data="{ 
    open: {{ session()->has('success') ? 'true' : 'false' }},
    message: '{{ session('success') ?? '' }}'
}"
@notify.window="if ($event.detail.type === 'success') { message = $event.detail.message; open = true; }"
class="relative">
    <!-- Trigger Slot -->
    @if(isset($trigger))
        <div @click="open = true" class="inline-block">
            {{ $trigger }}
        </div>
    @endif

    <!-- Modal Teleported to Body -->
    <template x-teleport="body">
        <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            
            <!-- Backdrop Overlay -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/80 backdrop-blur-sm"
                 @click="open = false"></div>

            <!-- Content Card -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative z-50 grid w-full max-w-lg gap-4 border border-slate-200 bg-white p-6 shadow-lg sm:rounded-lg dark:border-slate-800 dark:bg-slate-950"
                 @click.outside="open = false">
                 
                 <!-- Header -->
                 <div class="flex flex-col space-y-2 text-center sm:text-left">
                     <div class="flex items-center gap-2">
                         @if(isset($icon))
                             <div class="text-slate-500 dark:text-slate-400 shrink-0">
                                 {{ $icon }}
                             </div>
                         @endif
                         <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-50">
                             {{ $title }}
                         </h2>
                     </div>
                     <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed" x-text="message ? message : '{{ $description ?? '' }}'">
                         {{ $description ?? '' }}
                     </p>
                 </div>

                 <!-- Footer -->
                 <div class="flex flex-col-reverse sm:flex-row sm:justify-end sm:space-x-2 gap-2 sm:gap-0 mt-2">
                     {{ $footer }}
                 </div>
            </div>

        </div>
    </template>
</div>
