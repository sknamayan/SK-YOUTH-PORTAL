@php
    $pendingServiceRequestsCount = \App\Models\CustomRequest::where('status', 'pending')->count()
        + \App\Models\HealthRequest::where('status', 'pending')->count()
        + \App\Models\MedicineRequest::where('status', 'pending')->count()
        + \App\Models\SilidKarununganRequest::where('status', 'pending')->count();
    
    $pendingKkProfilesCount = \App\Models\KkProfile::where('status', 'pending')->count();
    
    $pendingSportsRegistrationsCount = \App\Models\SportsRegistration::where('status', 'pending')->count();
    
    $pendingConsultationsCount = \App\Models\ConsultationRequest::where('status', 'Open')->count();

    $pendingUserApprovalsCount = \App\Models\User::where('is_approved', false)->count();
@endphp

<!-- Centralized Left Sidebar Navigation -->
<div x-data="{ mobileSidebar: false }"
     @toggle-sidebar.window="mobileSidebar = !mobileSidebar"
     x-cloak>
    <div x-show="mobileSidebar"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileSidebar = false"
         class="fixed inset-0 bg-slate-950/45 backdrop-blur-[2px] z-20 md:hidden"></div>

    <aside :class="mobileSidebar ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
       class="fixed md:sticky md:self-start inset-y-0 md:bottom-auto left-0 w-[78%] max-w-[300px] md:w-64 lg:w-72 xl:w-80 md:max-w-none md:top-16 md:h-[calc(100vh_-_4rem)] border-r border-slate-100 bg-white z-30 transition-transform duration-300 transform flex flex-col justify-between shrink-0 shadow-sm md:shadow-none">

    <!-- Top Menu / Navigation Scrollable Pane -->
    <div class="flex-1 p-6 space-y-6 overflow-y-auto">
        <!-- Sidebar Logo -->
        <div class="flex items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div class="flex items-center space-x-3 min-w-0">
                <img src="{{ asset('images/logo.png') }}" class="w-11 h-11 object-contain rounded-full bg-white p-0.5 border shadow-sm shrink-0" alt="SK Logo">
                <div class="min-w-0">
                    <h2 class="text-sm font-bold text-slate-800 font-display uppercase tracking-tight truncate">SK Namayan</h2>
                    <span class="text-[9px] font-black tracking-widest text-[#1e40af] uppercase block">Admin Control</span>
                </div>
            </div>
            <button @click="mobileSidebar = false" type="button" class="md:hidden p-2 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition" aria-label="Close sidebar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Menu links -->
        <nav class="space-y-1">
            <!-- Dashboard Link -->
            <a href="{{ route('dashboard.index') }}"
               class="flex items-center justify-between px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('dashboard.index') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <div class="flex items-center space-x-3">
                    <x-category-icon name="dashboard" class="w-6 h-6" />
                    <span>Dashboard</span>
                </div>
            </a>


            <!-- Service Requests Link with pending requests count badge -->
            <a href="{{ route('dashboard.requests.index') }}"
               class="flex items-center justify-between px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('dashboard.requests.index') || request()->routeIs('dashboard.requests.show') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <div class="flex items-center space-x-3">
                    <x-category-icon name="users" class="w-6 h-6" />
                    <span>Service Requests</span>
                </div>
                @if(isset($pendingServiceRequestsCount) && $pendingServiceRequestsCount > 0)
                    <span class="bg-rose-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full shadow-sm select-none">{{ $pendingServiceRequestsCount }}</span>
                @endif
            </a>

            <!-- Profiling List Link -->
            <a href="{{ route('dashboard.profiling.index') }}"
               class="flex items-center justify-between px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('dashboard.profiling.index') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <div class="flex items-center space-x-3">
                    <x-category-icon name="logs" class="w-6 h-6" />
                    <span>Profiling List</span>
                </div>
                @if(isset($pendingKkProfilesCount) && $pendingKkProfilesCount > 0)
                    <span class="bg-rose-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full shadow-sm select-none">{{ $pendingKkProfilesCount }}</span>
                @endif
            </a>

            <!-- Master Calendar Link -->
            <a href="{{ route('dashboard.calendar.index') }}"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('dashboard.calendar.*') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <x-category-icon name="sports" class="w-6 h-6" />
                <span>Master Calendar</span>
            </a>

            <!-- Sports League Link -->
            @if(Auth::user()->isAdmin() || Auth::user()->isDpo())
            <a href="{{ route('admin.sports-league.index') }}"
               class="flex items-center justify-between px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('admin.sports-league.*') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <div class="flex items-center space-x-3">
                    <x-category-icon name="sports" class="w-6 h-6" />
                    <span>SIKLAB</span>
                </div>
                @if(isset($pendingSportsRegistrationsCount) && $pendingSportsRegistrationsCount > 0)
                    <span class="bg-rose-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full shadow-sm select-none">{{ $pendingSportsRegistrationsCount }}</span>
                @endif
            </a>
            @endif

            <!-- SKonsulta Chats Link -->
            @if(Auth::user()->isAdmin() || Auth::user()->isDpo())
            <a href="{{ route('admin.consultations.index') }}"
               class="flex items-center justify-between px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('admin.consultations.*') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <div class="flex items-center space-x-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>SKonsulta Chats</span>
                </div>
                @if(isset($pendingConsultationsCount) && $pendingConsultationsCount > 0)
                    <span class="bg-rose-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full shadow-sm select-none">{{ $pendingConsultationsCount }}</span>
                @endif
            </a>
            @endif

            <!-- Partnerships Link -->
            @if(Route::has('admin.partners.index') && Auth::user()->isSuperAdmin())
            <a href="{{ route('admin.partners.index') }}"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('admin.partners.*') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <x-category-icon name="peace-building" class="w-6 h-6" />
                <span>Partnerships</span>
            </a>
            @endif

            <!-- Reports Link -->
            @if(Route::has('admin.reports.index') && Auth::user()->isAdmin())
            <a href="{{ route('admin.reports.index') }}"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('admin.reports.*') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <x-category-icon name="education" class="w-6 h-6" />
                <span>Reports</span>
            </a>
            @endif

            <!-- Portal Structure Link -->
            @if(Auth::user()->isSuperAdmin())
            <a href="{{ route('admin.structure.index') }}"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('admin.structure.*') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <x-category-icon name="governance" class="w-6 h-6" />
                <span>Portal Structure</span>
            </a>
            @endif

            <!-- Audit Logs Link -->
            @if(Auth::user()->isSuperAdmin())
            <a href="{{ route('admin.logs.index') }}"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('admin.logs.*') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <x-category-icon name="logs" class="w-6 h-6" />
                <span>Audit Logs</span>
            </a>
            @endif

            <!-- Hero Slides Link -->
            @if(Auth::user()->isSuperAdmin())
            <a href="{{ route('admin.carousel.index') }}"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('admin.carousel.*') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <x-category-icon name="carousel" class="w-6 h-6" />
                <span>Hero Slides</span>
            </a>
            @endif

            <!-- Community Board Dropdown -->
            @if(Auth::user()->isAdmin())
            <div x-data="{ isCommunityBoardOpen: {{ request()->routeIs('admin.news.*') || request()->routeIs('admin.officials.*') || request()->routeIs('admin.transparency.*') ? 'true' : 'false' }} }" class="space-y-1">
                <button
                    class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('admin.news.*') || request()->routeIs('admin.officials.*') || request()->routeIs('admin.transparency.*') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    @click="isCommunityBoardOpen = !isCommunityBoardOpen"
                    :aria-expanded="isCommunityBoardOpen"
                >
                    <div class="flex items-center space-x-3">
                        <x-category-icon name="website" class="w-6 h-6" />
                        <span>Community Board</span>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': isCommunityBoardOpen }">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="isCommunityBoardOpen" x-collapse class="pl-6 space-y-1" style="display: none;">
                    <a href="{{ route('admin.news.index') }}"
                       class="flex items-center space-x-3 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('admin.news.*') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <x-category-icon name="logs" class="w-6 h-6" />
                        <span>News Articles</span>
                    </a>

                    <a href="{{ route('admin.transparency.index') }}"
                       class="flex items-center space-x-3 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('admin.transparency.*') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <x-category-icon name="governance" class="w-6 h-6" />
                        <span>Transparency Board</span>
                    </a>

                    <a href="{{ route('admin.officials.index') }}"
                       class="flex items-center space-x-3 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('admin.officials.*') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <x-category-icon name="users" class="w-6 h-6" />
                        <span>SK Officials</span>
                    </a>
                </div>
            </div>
            @endif

            @if(Auth::user()->isSuperAdmin())
                <!-- Collapsible Settings Dropdown for Superadmin -->
                <div x-data="{ isOpened: {{ request()->routeIs('admin.users.*') || request()->routeIs('profile.edit') || request()->routeIs('admin.recycle-bin.*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button
                        class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('admin.users.*') || request()->routeIs('profile.edit') || request()->routeIs('admin.recycle-bin.*') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                        @click="isOpened = !isOpened"
                        :aria-expanded="isOpened"
                    >
                        <div class="flex items-center space-x-3">
                            <x-category-icon name="profile" class="w-6 h-6" />
                            <span>Settings</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if(isset($pendingUserApprovalsCount) && $pendingUserApprovalsCount > 0)
                                <span x-show="!isOpened" class="bg-rose-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full shadow-sm select-none">{{ $pendingUserApprovalsCount }}</span>
                            @endif
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': isOpened }">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>

                    <div x-show="isOpened" x-collapse class="pl-6 space-y-1" style="display: none;">
                        <!-- User Accounts -->
                        <a href="{{ route('admin.users.index') }}"
                           class="flex items-center justify-between px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <div class="flex items-center space-x-3">
                                <x-category-icon name="users" class="w-6 h-6" />
                                <span>Account Management</span>
                            </div>
                            @if(isset($pendingUserApprovalsCount) && $pendingUserApprovalsCount > 0)
                                <span class="bg-rose-600 text-white text-[9px] font-black px-2 py-0.5 rounded-full shadow-sm select-none">{{ $pendingUserApprovalsCount }}</span>
                            @endif
                        </a>

                        <!-- Master Recycle Bin -->
                        <a href="{{ route('admin.recycle-bin.index') }}"
                           class="flex items-center space-x-3 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('admin.recycle-bin.*') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <x-category-icon name="logs" class="w-6 h-6" />
                            <span>Recycle Bin</span>
                        </a>

                        <!-- My Profile -->
                        <a href="{{ route('profile.edit') }}"
                           class="flex items-center space-x-3 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('profile.edit') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <x-category-icon name="profile" class="w-6 h-6" />
                            <span>My Profile</span>
                        </a>
                    </div>
                </div>
            @else
                <!-- Standalone My Profile Link for Admin / Staff -->
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center space-x-3 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition {{ request()->routeIs('profile.edit') ? 'bg-blue-50 text-[#1e40af]' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <x-category-icon name="profile" class="w-6 h-6" />
                    <span>My Profile</span>
                </a>
            @endif

            <!-- View Website Link -->
            <a href="/"
               class="flex items-center space-x-3 px-4 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                <x-category-icon name="website" class="w-6 h-6" />
                <span>View Website</span>
            </a>
        </nav>
    </div>

    <!-- Fixed User Profile Footer -->
    <div class="px-6 py-12 bg-slate-50 border-t border-slate-100 flex items-center justify-between shrink-0 relative" x-data="{ isProfileActive: false }">
        <div class="flex items-center space-x-3 min-w-0">
            <div class="w-9 h-9 rounded-full bg-[#1e40af] text-white font-extrabold text-xs flex items-center justify-center font-display shadow-sm shrink-0">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="truncate">
                <span class="block text-xs font-bold text-slate-800 truncate">{{ Auth::user()->name }}</span>
                <span class="block text-[9px] font-black uppercase tracking-wider text-slate-400">{{ Auth::user()->role }}</span>
            </div>
        </div>

    </div>
</aside>
</div>
