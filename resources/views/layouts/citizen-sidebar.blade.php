<!-- Citizen Left Sidebar Navigation -->
<aside :class="mobileSidebar ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
       class="fixed md:sticky md:self-start inset-y-0 md:bottom-auto left-0 w-[86%] max-w-[300px] sm:w-[80%] md:w-80 md:max-w-none md:top-16 md:h-[calc(100vh_-_4rem)] border-r border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 z-30 transition-transform duration-300 transform flex flex-col justify-between shrink-0 shadow-sm md:shadow-none overflow-x-hidden">

    <!-- Top Menu / Navigation Scrollable Pane -->
    <div class="flex-1 p-4 sm:p-6 space-y-5 sm:space-y-6 overflow-y-auto overflow-x-hidden">
        <!-- Sidebar Logo -->
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 dark:border-slate-800 min-w-0">
            <img src="{{ asset('images/logo.png') }}" class="w-11 h-11 object-contain rounded-full bg-white p-0.5 border shadow-sm shrink-0" alt="SK Logo">
            <div class="min-w-0">
                <h2 class="text-sm font-bold text-slate-800 dark:text-slate-200 font-display uppercase tracking-tight leading-tight">SK Namayan</h2>
                <span class="text-[9px] font-black tracking-widest text-[#1e40af] dark:text-blue-400 uppercase block">Citizen Portal</span>
            </div>
        </div>

        <!-- Menu links -->
        <nav class="space-y-1.5">
            <!-- My Requests Link -->
            <a href="{{ route('profile.my-requests') }}"
               class="flex items-center justify-between w-full px-4 py-2.5 rounded-xl font-bold text-[10px] sm:text-xs uppercase tracking-wider transition {{ request()->routeIs('profile.my-requests') ? 'bg-blue-50 dark:bg-blue-955/40 text-[#1e40af] dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <x-category-icon name="dashboard" class="w-4 h-4 shrink-0" />
                    <span class="leading-snug break-words">My Requests</span>
                </div>
            </a>

            <!-- Notifications Link -->
            <a href="{{ route('notifications.index') }}"
               class="flex items-center justify-between w-full px-4 py-2.5 rounded-xl font-bold text-[10px] sm:text-xs uppercase tracking-wider transition {{ request()->routeIs('notifications.index') ? 'bg-blue-50 dark:bg-blue-955/40 text-[#1e40af] dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="leading-snug break-words">Notifications</span>
                </div>
                @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                    <span class="bg-rose-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full shadow-sm select-none">{{ $unreadNotificationsCount }}</span>
                @endif
            </a>


            <!-- Sports League Link -->
            <a href="{{ route('forms.sports.create') }}"
               class="flex items-center justify-between w-full px-4 py-2.5 rounded-xl font-bold text-[10px] sm:text-xs uppercase tracking-wider transition {{ request()->url() === route('forms.sports.create') || request()->query('form') === 'sports' ? 'bg-blue-50 dark:bg-blue-950/40 text-[#1e40af] dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white' }}">
                <div class="flex items-center gap-3 min-w-0">
                    <x-category-icon name="sports" class="w-4 h-4 shrink-0" />
                    <span class="leading-snug break-words">Sports League</span>
                </div>
            </a>



            <!-- KK Self Profiling Link -->
            <a href="{{ route('profile.profiling.create') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-bold text-[10px] sm:text-xs uppercase tracking-wider transition {{ request()->routeIs('profile.profiling.create') ? 'bg-blue-50 dark:bg-blue-950/40 text-[#1e40af] dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white' }}">
                <x-category-icon name="users" class="w-4 h-4 shrink-0" />
                <span class="leading-snug break-words">KK Self-Profiling</span>
            </a>

            <!-- Account Settings Link -->
            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-bold text-[10px] sm:text-xs uppercase tracking-wider transition {{ request()->routeIs('profile.edit') ? 'bg-blue-50 dark:bg-blue-950/40 text-[#1e40af] dark:text-blue-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white' }}">
                <x-category-icon name="profile" class="w-4 h-4 shrink-0" />
                <span class="leading-snug break-words">Account Settings</span>
            </a>

            <!-- View Website Link -->
            <a href="/"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl font-bold text-[10px] sm:text-xs uppercase tracking-wider transition text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-850 hover:text-slate-900 dark:hover:text-white">
                <x-category-icon name="website" class="w-4 h-4 shrink-0" />
                <span class="leading-snug break-words">View Website</span>
            </a>
        </nav>
    </div>

    <!-- Fixed User Profile Footer -->
    @auth
    <div class="px-4 sm:px-6 py-6 sm:py-8 bg-slate-50 dark:bg-slate-955 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0 relative">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-full bg-[#1e40af] dark:bg-blue-950 text-white dark:text-blue-300 font-extrabold text-xs flex items-center justify-center font-display shadow-sm shrink-0">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="min-w-0">
                <span class="block text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ Auth::user()->name }}</span>
                <span class="block text-[9px] font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">Citizen</span>
            </div>
        </div>
    </div>
    @endauth
</aside>
