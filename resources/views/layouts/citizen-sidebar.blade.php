<!-- Citizen Left Sidebar Navigation -->
<aside :class="mobileSidebar ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
       class="fixed md:sticky md:self-start inset-y-0 md:bottom-auto left-0 w-[86%] max-w-[300px] sm:w-[80%] md:w-80 md:max-w-none md:top-16 md:h-[calc(100vh_-_4rem)] border-r border-slate-100 bg-white z-30 transition-transform duration-300 transform flex flex-col justify-between shrink-0 shadow-sm md:shadow-none overflow-x-hidden">

    <!-- Top Menu / Navigation Scrollable Pane -->
    <div class="flex-1 p-4 sm:p-6 space-y-5 sm:space-y-6 overflow-y-auto overflow-x-hidden">
        <!-- Sidebar Logo -->
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 min-w-0">
            <img src="{{ asset('images/logo.png') }}" class="w-11 h-11 object-contain rounded-full bg-white p-0.5 border shadow-sm shrink-0" alt="SK Logo">
            <div class="min-w-0">
                <h2 class="text-sm font-bold text-slate-800 font-display uppercase tracking-tight leading-tight">SK Namayan</h2>
                <span class="text-[9px] font-black tracking-widest text-[#1e40af] uppercase block">Citizen Portal</span>
            </div>
        </div>

        <!-- Menu links -->
        <nav class="space-y-1.5">
            <!-- My Requests Link -->
            <a href="{{ route('profile.my-requests') }}"
               class="flex items-center justify-between w-full px-4 py-2.5 rounded-xl font-bold text-[10px] sm:text-xs uppercase tracking-wider transition {{ request()->routeIs('profile.my-requests') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <x-category-icon name="dashboard" class="w-4 h-4 shrink-0" />
                    <span class="leading-snug break-words">My Requests</span>
                </div>
            </a>

            <!-- Sports League Link -->
            <a href="{{ route('forms.sports.create') }}"
               class="flex items-center justify-between w-full px-4 py-2.5 rounded-xl font-bold text-[10px] sm:text-xs uppercase tracking-wider transition {{ request()->url() === route('forms.sports.create') || request()->query('form') === 'sports' ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <x-category-icon name="sports" class="w-4 h-4 shrink-0" />
                    <span class="leading-snug break-words">SIKLAB</span>
                </div>
            </a>

            <!-- KK Self Profiling Link -->
            <a href="{{ route('profile.profiling.create') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-bold text-[10px] sm:text-xs uppercase tracking-wider transition {{ request()->routeIs('profile.profiling.create') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <x-category-icon name="users" class="w-4 h-4 shrink-0" />
                <span class="leading-snug break-words">KK Self-Profiling</span>
            </a>

            <!-- Account Settings Link -->
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-bold text-[10px] sm:text-xs uppercase tracking-wider transition {{ request()->routeIs('profile.edit') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <x-category-icon name="profile" class="w-4 h-4 shrink-0" />
                <span class="leading-snug break-words">Account Settings</span>
            </a>

            <!-- View Website Link -->
            <a href="/"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-bold text-[10px] sm:text-xs uppercase tracking-wider transition text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                <x-category-icon name="website" class="w-4 h-4 shrink-0" />
                <span class="leading-snug break-words">View Website</span>
            </a>
        </nav>
    </div>

    <!-- Fixed User Profile Footer -->
    @auth
    <div class="px-4 sm:px-6 py-6 sm:py-8 bg-slate-50 border-t border-slate-100 flex items-center justify-between shrink-0 relative">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-full bg-[#1e40af] text-white font-extrabold text-xs flex items-center justify-center font-display shadow-sm shrink-0">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="min-w-0">
                <span class="block text-xs font-bold text-slate-800 truncate">{{ Auth::user()->name }}</span>
                <span class="block text-[9px] font-black uppercase tracking-wider text-slate-400">Citizen</span>
            </div>
        </div>
    </div>
    @endauth
</aside>
