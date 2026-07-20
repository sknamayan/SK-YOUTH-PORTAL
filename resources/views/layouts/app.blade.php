<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SK Namayan') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- PWA / Add to Home Screen Setup -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#1e40af">

    <!-- iOS (iPhone/iPad) PWA Support -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SK Namayan">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">

    <!-- Dark Mode Guard Script -->
    <script>
        window.SKTheme = {
            resolveTheme: function(themePreference) {
                if (themePreference === 'dark' || themePreference === 'light') {
                    return themePreference;
                }

                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            },
            setTheme: function(themePreference) {
                const normalizedPreference = ['dark', 'light', 'system'].includes(themePreference) ?
                    themePreference :
                    'system';
                const resolvedTheme = this.resolveTheme(normalizedPreference);

                localStorage.setItem('theme', normalizedPreference);
                document.documentElement.classList.toggle('dark', resolvedTheme === 'dark');
                window.dispatchEvent(new CustomEvent('theme:changed', {
                    detail: {
                        theme: normalizedPreference,
                        resolvedTheme: resolvedTheme,
                    }
                }));

                return resolvedTheme;
            },
            isDark: function() {
                return this.resolveTheme(localStorage.getItem('theme')) === 'dark';
            }
        };

        (function() {
            const dbTheme = '{{ auth()->check() ? auth()->user()->theme : 'system' }}';
            const currentTheme = ['dark', 'light', 'system'].includes(dbTheme) ? dbTheme : 'system';

            let savedTheme = localStorage.getItem('theme');
            if (!savedTheme) {
                savedTheme = currentTheme;
                localStorage.setItem('theme', savedTheme);
            }

            window.SKTheme.setTheme(savedTheme);
        })();
    </script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        [x-cloak] {
            display: none !important;
        }

        .no-scrollbar::-webkit-scrollbar,
        .scrollbar-none::-webkit-scrollbar,
        .scrollbar-hide::-webkit-scrollbar {
            display: none !important;
        }

        .no-scrollbar,
        .scrollbar-none,
        .scrollbar-hide {
            -ms-overflow-style: none !important;
            /* IE and Edge */
            scrollbar-width: none !important;
            /* Firefox */
        }
    </style>
</head>

<body
    class="bg-[#fefefe] dark:bg-slate-950 text-slate-900 dark:text-slate-100 font-sans antialiased flex flex-col min-h-screen overflow-x-hidden">
    <!-- Global Skeleton Loader (Fades out when window is fully loaded) -->
    <div id="global-page-loader"
        class="fixed inset-0 z-[9999] bg-[#fefefe] dark:bg-slate-950 flex flex-col pointer-events-none transition-opacity duration-300 opacity-0 md:hidden">
        <!-- Header skeleton -->
        <div
            class="h-16 bg-[#1e40af] dark:bg-slate-900 border-b border-blue-800 dark:border-slate-800 flex items-center px-4 md:px-8 shrink-0">
            <div class="w-10 h-10 rounded-full skeleton-shimmer bg-white/20 shrink-0"></div>
            <div class="w-32 h-5 rounded skeleton-shimmer bg-white/20 ml-3"></div>
        </div>

        @if (request()->routeIs('dashboard', 'dashboard.*', 'admin.*') ||
                (auth()->check() && auth()->user()->canAccessDashboard() && request()->routeIs('profile.edit')))
            <!-- 1. Admin/Official Control Center Skeleton -->
            <div class="flex flex-1 min-h-0">
                <!-- Sidebar skeleton -->
                <div
                    class="w-80 border-r border-slate-100 dark:border-slate-800 p-6 space-y-6 hidden md:block shrink-0 animate-fade-in-up">
                    <div class="w-full h-12 rounded-xl skeleton-shimmer"></div>
                    <div class="space-y-3.5 pt-4">
                        <div class="w-4/5 h-8 rounded-xl skeleton-shimmer"></div>
                        <div class="w-full h-8 rounded-xl skeleton-shimmer"></div>
                        <div class="w-3/4 h-8 rounded-xl skeleton-shimmer"></div>
                        <div class="w-5/6 h-8 rounded-xl skeleton-shimmer"></div>
                    </div>
                </div>
                <!-- Main Content skeleton -->
                <div class="flex-1 p-6 md:p-8 space-y-6 overflow-hidden">
                    <div class="flex justify-between items-center">
                        <div class="space-y-2">
                            <div class="w-48 h-6 rounded-lg skeleton-shimmer"></div>
                            <div class="w-72 h-3.5 rounded-lg skeleton-shimmer"></div>
                        </div>
                        <div class="w-28 h-10 rounded-xl skeleton-shimmer"></div>
                    </div>
                    <!-- Metrics grid -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="h-24 rounded-2xl border border-slate-100 dark:border-slate-800 skeleton-shimmer">
                        </div>
                        <div class="h-24 rounded-2xl border border-slate-100 dark:border-slate-800 skeleton-shimmer">
                        </div>
                        <div class="h-24 rounded-2xl border border-slate-100 dark:border-slate-800 skeleton-shimmer">
                        </div>
                        <div class="h-24 rounded-2xl border border-slate-100 dark:border-slate-800 skeleton-shimmer">
                        </div>
                    </div>
                    <!-- Table skeleton -->
                    <div class="h-96 rounded-2xl border border-slate-100 dark:border-slate-800 skeleton-shimmer"></div>
                </div>
            </div>
        @elseif(request()->routeIs('profile.my-requests'))
            <!-- 2. Citizen Requests Dashboard Skeleton -->
            <div class="max-w-7xl mx-auto w-full p-6 md:p-8 space-y-6 flex-1 overflow-hidden">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="space-y-2">
                        <div class="w-48 h-6 rounded-lg skeleton-shimmer"></div>
                        <div class="w-72 h-3.5 rounded-lg skeleton-shimmer"></div>
                    </div>
                    <div class="w-28 h-10 rounded-xl skeleton-shimmer"></div>
                </div>
                <!-- Metrics grid -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="h-24 rounded-2xl border border-slate-100 dark:border-slate-800 skeleton-shimmer"></div>
                    <div class="h-24 rounded-2xl border border-slate-100 dark:border-slate-800 skeleton-shimmer"></div>
                    <div class="h-24 rounded-2xl border border-slate-100 dark:border-slate-800 skeleton-shimmer"></div>
                    <div class="h-24 rounded-2xl border border-slate-100 dark:border-slate-800 skeleton-shimmer"></div>
                </div>
                <!-- Table card skeleton -->
                <div
                    class="h-96 rounded-2xl md:rounded-3xl border border-slate-100 dark:border-slate-800 skeleton-shimmer">
                </div>
            </div>
        @else
            <!-- 3. Public Landing / General Pages Skeleton -->
            <div class="max-w-7xl mx-auto w-full p-6 md:p-8 space-y-8 flex-1 overflow-hidden">
                <!-- Hero Banner skeleton -->
                <div class="w-full h-56 md:h-72 rounded-3xl skeleton-shimmer"></div>

                <!-- Cards grid skeleton (representing News / Services / Committees) -->
                <div class="space-y-4">
                    <div class="w-48 h-6 rounded-lg skeleton-shimmer"></div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="h-44 rounded-2xl border border-slate-100 dark:border-slate-800 skeleton-shimmer">
                        </div>
                        <div class="h-44 rounded-2xl border border-slate-100 dark:border-slate-800 skeleton-shimmer">
                        </div>
                        <div class="h-44 rounded-2xl border border-slate-100 dark:border-slate-800 skeleton-shimmer">
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        (function() {
            var loader = document.getElementById('global-page-loader');
            var showTimeout = setTimeout(function() {
                if (loader) {
                    loader.classList.remove('opacity-0');
                }
            }, 150); // Delay showing the loader by 150ms to prevent flashing on fast page renders

            var fadeLoader = function() {
                clearTimeout(showTimeout);
                if (loader) {
                    loader.style.opacity = '0';
                    setTimeout(function() {
                        if (loader.parentNode) {
                            loader.parentNode.removeChild(loader);
                        }
                    }, 300);
                }
            };
            window.addEventListener('load', fadeLoader);
            // Fallback fade out after 2 seconds if load event doesn't fire
            setTimeout(fadeLoader, 2000);
        })();
    </script>

    @php
        $hideFooter =
            request()->routeIs('dashboard', 'dashboard.*', 'admin.*') ||
            (auth()->check() && auth()->user()->canAccessDashboard() && request()->routeIs('profile.edit'));
    @endphp
    <!-- Sticky Header Navbar -->
    <nav x-data="{ mobileMenuOpen: false }" class="bg-[#1e40af] text-white sticky top-0 z-40 shadow-lg border-b border-blue-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between relative">

            <!-- Left: Burger & Branding with Logo -->
            <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
                <!-- Burger Icon (Always available on mobile; hidden on dashboard desktop) -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                    class="inline-flex shrink-0 text-blue-100 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/50 p-1.5 rounded-xl hover:bg-white/10 transition {{ request()->routeIs('dashboard', 'dashboard.*', 'admin.*') || (auth()->check() && auth()->user()->canAccessDashboard() && request()->routeIs('profile.edit')) ? 'hidden' : '' }}"
                    aria-label="Toggle menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Dashboard Burger Icon (Visible only on mobile/tablet for dashboard/admin routes) -->
                @if (request()->routeIs('dashboard', 'dashboard.*', 'admin.*') ||
                        (auth()->check() && auth()->user()->canAccessDashboard() && request()->routeIs('profile.edit')))
                    <button @click="$dispatch('toggle-sidebar')" type="button"
                        class="inline-flex md:hidden text-blue-100 hover:text-white focus:outline-none focus:ring-2 focus:ring-white/50 p-1.5 rounded-xl hover:bg-white/10 transition"
                        aria-label="Toggle Dashboard Sidebar">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                @endif

                <!-- Branding -->
                <a href="/" class="flex items-center space-x-2.5 group min-w-0">
                    <img src="{{ asset('images/logo.png') }}"
                        class="w-10 h-10 object-contain rounded-full bg-white p-0.5 border border-blue-200 shadow-sm transition group-hover:scale-105 shrink-0"
                        alt="SK Namayan Logo">
                    <span
                        class="text-sm font-extrabold tracking-wider text-white uppercase font-display truncate max-w-[8rem] sm:max-w-none">SK
                        Namayan</span>
                </a>
            </div>


            <!-- Right: Nav options & dropdowns -->
            <div class="flex items-center space-x-2 sm:space-x-3 text-sm shrink-0 min-w-0" x-data="{
                darkMode: window.SKTheme.isDark(),
                notifOpen: false,
                profileOpen: false
            }"
                x-init="$watch('darkMode', val => {
                    window.SKTheme.setTheme(val ? 'dark' : 'light');
                });

                window.addEventListener('theme:changed', () => {
                    darkMode = window.SKTheme.isDark();
                });">

                <!-- Theme Toggle Switch -->
                <button @click="darkMode = !darkMode" type="button"
                    class="p-2 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 transition focus:outline-none"
                    aria-label="Toggle theme">
                    <!-- Moon Icon (Light Mode active, click to set Dark Mode) -->
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <!-- Sun Icon (Dark Mode active, click to set Light Mode) -->
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M14 12a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </button>

                @if (Route::has('login'))
                    @auth
                        @if (Auth::user()->canAccessDashboard())
                            <a href="{{ route('dashboard.index') }}"
                                class="hidden md:inline-flex items-center space-x-1.5 px-3 py-1.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition mr-1">
                                <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                                </svg>
                                <span>Dashboard</span>
                            </a>
                        @endif

                        <!-- Notification Center Dropdown -->
                        <div class="relative">
                            @php
                                $unreadCount = Auth::user()->notifications()->whereNull('read_at')->count();
                                $notifications = Auth::user()->notifications()->take(5)->get();
                            @endphp
                            <button @click="notifOpen = !notifOpen; profileOpen = false" type="button"
                                class="p-2 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 transition focus:outline-none relative overflow-visible">
                                <!-- Bell Icon -->
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @if (($notificationsCount ?? ($unreadMessagesCount ?? ($unreadCount ?? 0))) > 0)
                                    <span
                                        class="absolute top-0 right-0 transform translate-x-1/2 -translate-y-1/2 flex h-5 min-w-[1.25rem] px-1.5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-sm select-none z-20 animate-pulse">
                                        {{ $notificationsCount ?? ($unreadMessagesCount ?? $unreadCount) }}
                                    </span>
                                @endif
                            </button>

                            <!-- Notifications Dropdown Panel -->
                            <div x-show="notifOpen" @click.away="notifOpen = false"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 mt-0"
                                x-transition:enter-end="opacity-100 scale-100 mt-2"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 mt-2"
                                x-transition:leave-end="opacity-0 scale-95 mt-0"
                                class="absolute right-[-2rem] sm:right-0 origin-top-right top-full mt-2 w-[90vw] max-w-[90vw] sm:w-80 sm:max-w-xs md:w-96 md:max-w-md rounded-2xl border border-slate-100 bg-white shadow-lg ring-1 ring-slate-200 z-50 text-slate-800 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-100 dark:ring-slate-700"
                                x-cloak>
                                <div
                                    class="px-4 pb-2 border-b border-slate-100 dark:border-slate-850 flex items-center justify-between">
                                    <span
                                        class="font-bold text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Notifications</span>
                                    @if ($unreadCount > 0)
                                        <form method="POST" action="{{ route('notifications.read-all') }}">
                                            @csrf
                                            <button type="submit"
                                                class="text-[10px] text-blue-600 dark:text-blue-400 font-bold hover:underline">
                                                Mark all read
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                <div class="flex flex-col gap-2 p-2">
                                    @forelse($notifications as $notif)
                                        <form method="POST" action="{{ route('notifications.read', $notif) }}"
                                            class="block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="w-full rounded-xl border border-slate-100 px-3 py-3 text-left transition hover:bg-slate-50 dark:border-slate-800 dark:hover:bg-slate-800/60 flex flex-col gap-1.5">
                                                <div class="flex items-start justify-between gap-2">
                                                    <span
                                                        class="font-semibold text-sm {{ $notif->read_at ? 'text-slate-500 dark:text-slate-400' : 'text-slate-800 dark:text-white' }}">
                                                        {{ $notif->title }}
                                                    </span>
                                                    @if (!$notif->read_at)
                                                        <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-blue-600"></span>
                                                    @endif
                                                </div>
                                                <p class="text-xs leading-relaxed text-slate-500 dark:text-slate-400">
                                                    {{ $notif->message }}
                                                </p>
                                                <span
                                                    class="text-[10px] text-slate-400">{{ $notif->created_at->diffForHumans() }}</span>
                                            </button>
                                        </form>
                                    @empty
                                        <div
                                            class="rounded-xl border border-slate-100 px-3 py-6 text-center text-xs text-slate-400 dark:border-slate-800 dark:text-slate-500">
                                            No notifications yet.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- User Profile Dropdown -->
                        <div class="relative">
                            @php
                                $initials = '';
                                $user = Auth::user();
                                $initials = strtoupper(
                                    substr($user->first_name ?? $user->name, 0, 1) .
                                        substr($user->last_name ?? '', 0, 1),
                                );
                            @endphp
                            <button @click="profileOpen = !profileOpen; notifOpen = false" type="button"
                                class="flex items-center focus:outline-none active:scale-95 transition"
                                aria-label="User Menu">
                                <div
                                    class="w-8 h-8 rounded-full bg-white dark:bg-slate-800 text-[#1e40af] dark:text-blue-400 font-extrabold text-xs flex items-center justify-center border border-white/20 shadow-sm">
                                    {{ $initials }}
                                </div>
                            </button>

                            <!-- Dropdown Panel -->
                            <div x-show="profileOpen" @click.away="profileOpen = false"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95 mt-0"
                                x-transition:enter-end="opacity-100 scale-100 mt-2"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100 mt-2"
                                x-transition:leave-end="opacity-0 scale-95 mt-0"
                                class="absolute right-0 origin-top-right top-full mt-2 w-[90vw] sm:w-64 min-w-[200px] max-w-[95vw] bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl shadow-xl py-2 z-50 text-slate-800 dark:text-slate-100 overflow-hidden flex flex-col"
                                x-cloak>
                                <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-850">
                                    <p class="font-extrabold text-xs text-slate-850 dark:text-white truncate">
                                        {{ $user->name }}</p>
                                    <p class="text-[10px] text-slate-400 truncate">{{ $user->email }}</p>
                                </div>
                                <div class="py-1">
                                    @if ($user->canAccessDashboard())
                                        <a href="{{ route('dashboard.index') }}" @click="profileOpen = false"
                                            class="flex items-center space-x-2 px-4 py-2 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-850 transition">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                                            </svg>
                                            <span>Dashboard</span>
                                        </a>
                                    @else
                                        <a href="{{ route('profile.my-requests') }}" @click="profileOpen = false"
                                            class="flex items-center space-x-2 px-4 py-2 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-850 transition">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                            </svg>
                                            <span>My Requests</span>
                                        </a>
                                    @endif
                                    <a href="{{ route('profile.edit') }}" @click="profileOpen = false"
                                        class="flex items-center space-x-2 px-4 py-2 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-850 transition">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span>Account Settings</span>
                                    </a>
                                </div>
                                <div class="border-t border-slate-100 dark:border-slate-850 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left block px-4 py-3 text-sm text-white hover:bg-slate-700 transition-colors">
                                            <span class="flex items-center space-x-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                </svg>
                                                <span>Logout</span>
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center min-h-11 border border-white/20 hover:border-white text-white font-semibold py-1.5 px-3 md:px-4 rounded-xl transition hover:bg-white/5 text-xs">
                            Login
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="hidden sm:inline-flex bg-white text-[#1e40af] hover:bg-blue-50 font-bold py-1.5 px-4 rounded-xl transition border border-transparent shadow-sm">
                                Register
                            </a>
                        @endif
                    @endauth
                @endif
            </div>

        </div>

        <!-- Menu Drawer Overlay & Panel (Always active on trigger) -->
        <div x-show="mobileMenuOpen" class="fixed inset-0 z-50" x-cloak>

            <!-- Backdrop backdrop-blur-sm -->
            <div x-show="mobileMenuOpen" x-transition:enter="transition-opacity ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="mobileMenuOpen = false"
                class="fixed inset-0 bg-slate-955/60 backdrop-blur-sm"></div>

            <!-- Drawer Panel (Slides from left, full height, at least 70% width) -->
            <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                class="fixed inset-y-0 left-0 w-[75%] max-w-[320px] bg-gradient-to-b from-[#1e40af] to-[#0f172a] dark:from-slate-900 dark:to-slate-950 text-white shadow-2xl flex flex-col justify-between z-50 border-r border-white/10 dark:border-slate-800">

                <!-- Header inside Drawer -->
                <div
                    class="px-5 py-5 flex items-center justify-between border-b border-white/10 dark:border-slate-800">
                    <div class="flex items-center space-x-2.5">
                        <img src="{{ asset('images/logo.png') }}"
                            class="w-9 h-9 object-contain rounded-full bg-white p-0.5 shadow-md shadow-black/10"
                            alt="SK Namayan Logo">
                        <span class="text-xs font-black tracking-widest uppercase font-display text-white">SK
                            Namayan</span>
                    </div>
                    <button @click="mobileMenuOpen = false" type="button"
                        class="text-blue-100 hover:text-white focus:outline-none p-2 rounded-xl hover:bg-white/10 active:scale-95 transition-all"
                        aria-label="Close menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Menu Body -->
                <div class="flex-1 overflow-y-auto px-4 py-8 space-y-3" x-data="{ servicesOpen: false, committeesOpen: false, citizenOpen: false, adminOpen: false }">
                    <a href="/" @click="mobileMenuOpen = false"
                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                        <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Home</span>
                    </a>

                    <a href="{{ route('news.index') }}" @click="mobileMenuOpen = false"
                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                        <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 4a2 2 0 00-2 2v3m2-3a2 2 0 012 2v3a2 2 0 01-2 2h-2m-3-10V4m-4 12h4m-4-4h4" />
                        </svg>
                        <span>News Articles</span>
                    </a>

                    <a href="{{ route('officials.index') }}" @click="mobileMenuOpen = false"
                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                        <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z">
                            </path>
                        </svg>
                        <span>SK Officials</span>
                    </a>

                    <a href="{{ route('transparency.index') }}" @click="mobileMenuOpen = false"
                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                        <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span>Transparency Board</span>
                    </a>

                    <a href="{{ route('track.index') }}" @click="mobileMenuOpen = false"
                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                        <svg class="w-4 h-4 text-blue-255" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Track Request</span>
                    </a>

                    <!-- Our Services Dropdown -->
                    <div class="space-y-1 md:hidden">
                        <button @click="servicesOpen = !servicesOpen" type="button"
                            class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                            <div class="flex items-center space-x-3">
                                <svg class="w-4 h-4 text-blue-250" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span>Our Services</span>
                            </div>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200 text-blue-200"
                                :class="servicesOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="servicesOpen" x-transition class="pl-4 space-y-1 pb-2" x-cloak>
                            <a href="{{ route('projects.committee', ['project_slug' => 'sk-namayan-youth-services', 'committee_slug' => 'education']) }}"
                                @click="mobileMenuOpen = false"
                                class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 hover:bg-white/10 group active:scale-[0.98]">
                                <svg class="w-4 h-4 text-blue-300 group-hover:text-white transition shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <span
                                    class="text-xs font-semibold text-blue-100 group-hover:text-white transition leading-snug">Education
                                    & Library Services</span>
                            </a>
                            <a href="{{ route('projects.committee', ['project_slug' => 'sk-namayan-youth-services', 'committee_slug' => 'health']) }}"
                                @click="mobileMenuOpen = false"
                                class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 hover:bg-white/10 group active:scale-[0.98]">
                                <svg class="w-4 h-4 text-blue-300 group-hover:text-white transition shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span
                                    class="text-xs font-semibold text-blue-100 group-hover:text-white transition leading-snug">Health
                                    & Wellness</span>
                            </a>
                            <a href="{{ route('projects.committee', ['project_slug' => 'sk-namayan-youth-services', 'committee_slug' => 'governance']) }}"
                                @click="mobileMenuOpen = false"
                                class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 hover:bg-white/10 group active:scale-[0.98]">
                                <svg class="w-4 h-4 text-blue-300 group-hover:text-white transition shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span
                                    class="text-xs font-semibold text-blue-100 group-hover:text-white transition leading-snug">Patriotism,
                                    Governance & Active Citizenship</span>
                            </a>
                            <a href="{{ route('projects.committee', ['project_slug' => 'sk-namayan-youth-services', 'committee_slug' => 'active-citizenship']) }}"
                                @click="mobileMenuOpen = false"
                                class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 hover:bg-white/10 group active:scale-[0.98]">
                                <svg class="w-4 h-4 text-blue-300 group-hover:text-white transition shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span
                                    class="text-xs font-semibold text-blue-100 group-hover:text-white transition leading-snug">Active
                                    Citizenship & Leadership</span>
                            </a>
                            <a href="{{ route('projects.committee', ['project_slug' => 'sk-namayan-youth-services', 'committee_slug' => 'social-inclusion']) }}"
                                @click="mobileMenuOpen = false"
                                class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 hover:bg-white/10 group active:scale-[0.98]">
                                <svg class="w-4 h-4 text-blue-300 group-hover:text-white transition shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span
                                    class="text-xs font-semibold text-blue-100 group-hover:text-white transition leading-snug">Social
                                    Inclusion & Gender Equality</span>
                            </a>
                            <a href="{{ route('projects.committee', ['project_slug' => 'sk-namayan-youth-services', 'committee_slug' => 'peace-building']) }}"
                                @click="mobileMenuOpen = false"
                                class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 hover:bg-white/10 group active:scale-[0.98]">
                                <svg class="w-4 h-4 text-blue-300 group-hover:text-white transition shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <span
                                    class="text-xs font-semibold text-blue-100 group-hover:text-white transition leading-snug">Peace
                                    Building & Security</span>
                            </a>
                            <a href="{{ route('projects.committee', ['project_slug' => 'sk-namayan-youth-services', 'committee_slug' => 'environment']) }}"
                                @click="mobileMenuOpen = false"
                                class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 hover:bg-white/10 group active:scale-[0.98]">
                                <svg class="w-4 h-4 text-blue-300 group-hover:text-white transition shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707m2.828 9.9a5 5 0 110-7.07" />
                                </svg>
                                <span
                                    class="text-xs font-semibold text-blue-100 group-hover:text-white transition leading-snug">Climate
                                    Change & Disaster Risk Reduction</span>
                            </a>
                            <a href="{{ route('projects.committee', ['project_slug' => 'sk-namayan-youth-services', 'committee_slug' => 'youth-employment']) }}"
                                @click="mobileMenuOpen = false"
                                class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 hover:bg-white/10 group active:scale-[0.98]">
                                <svg class="w-4 h-4 text-blue-300 group-hover:text-white transition shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span
                                    class="text-xs font-semibold text-blue-100 group-hover:text-white transition leading-snug">Youth
                                    Employment & Livelihood</span>
                            </a>
                            <a href="{{ route('projects.committee', ['project_slug' => 'sk-namayan-youth-services', 'committee_slug' => 'agriculture']) }}"
                                @click="mobileMenuOpen = false"
                                class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 hover:bg-white/10 group active:scale-[0.98]">
                                <svg class="w-4 h-4 text-blue-300 group-hover:text-white transition shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 19l9 2m-9-2v-8m0 8l-9 2m9-2v-8m0 0a5 5 0 019 3m-9-3a5 5 0 00-9 3m9-3v-1" />
                                </svg>
                                <span
                                    class="text-xs font-semibold text-blue-100 group-hover:text-white transition leading-snug">Food
                                    Security & Agriculture</span>
                            </a>
                            <a href="{{ route('projects.committee', ['project_slug' => 'sk-namayan-youth-services', 'committee_slug' => 'global-mobility']) }}"
                                @click="mobileMenuOpen = false"
                                class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 hover:bg-white/10 group active:scale-[0.98]">
                                <svg class="w-4 h-4 text-blue-300 group-hover:text-white transition shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                </svg>
                                <span
                                    class="text-xs font-semibold text-blue-100 group-hover:text-white transition leading-snug">Global
                                    Mobility & Scholarship</span>
                            </a>
                        </div>
                    </div>

                    @if (Route::has('login'))
                        @auth
                            <!-- Citizen Portal Links (Flat, No Dropdown) -->
                            @if (!Auth::user()->canAccessDashboard())
                                <div class="space-y-1 border-t border-white/10 dark:border-slate-800 pt-3 mt-3">
                                    <span
                                        class="block px-4 pb-2 text-[9px] font-black text-blue-250 uppercase tracking-widest">Citizen
                                        Portal</span>
                                    <a href="{{ route('profile.my-requests') }}" @click="mobileMenuOpen = false"
                                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                                        <svg class="w-4 h-4 text-blue-205" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                        </svg>
                                        <span>My Requests</span>
                                    </a>
                                    <a href="{{ route('forms.sports.create') }}" @click="mobileMenuOpen = false"
                                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                                        <svg class="w-4 h-4 text-blue-205" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm0 13H9m3 0h3M5 7h14a2 2 0 012 2v2a7 7 0 01-14 0V9a2 2 0 012-2z">
                                            </path>
                                        </svg>
                                        <span>SIKLAB</span>
                                    </a>
                                    <a href="{{ route('profile.profiling.create') }}" @click="mobileMenuOpen = false"
                                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                                        <svg class="w-4 h-4 text-blue-205" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                        <span>KK Self-Profiling</span>
                                    </a>
                                    <a href="{{ route('profile.edit') }}" @click="mobileMenuOpen = false"
                                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                                        <svg class="w-4 h-4 text-blue-205" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0v-1" />
                                        </svg>
                                        <span>Account Settings</span>
                                    </a>
                                </div>
                            @endif

                            <!-- SK Officials Admin Control (Flat Links) -->
                            @if (Auth::user()->canAccessDashboard())
                                <div class="space-y-1 border-t border-white/10 dark:border-slate-800 pt-3 mt-3">
                                    <span
                                        class="block px-4 pb-2 text-[9px] font-black text-blue-250 uppercase tracking-widest">Admin
                                        Control</span>
                                    <a href="{{ route('dashboard.index') }}" @click="mobileMenuOpen = false"
                                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                                        <svg class="w-4 h-4 text-blue-205" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                                        </svg>
                                        <span>Dashboard Portal</span>
                                    </a>
                                    <a href="{{ route('profile.edit') }}" @click="mobileMenuOpen = false"
                                        class="flex items-center space-x-3 px-4 py-3 rounded-xl text-blue-100 hover:text-white hover:bg-white/10 hover:translate-x-1 font-bold font-display uppercase tracking-wider text-[11px] transition-all duration-300">
                                        <svg class="w-4 h-4 text-blue-205" fill="none" stroke="currentColor"
                                            stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0v-1" />
                                        </svg>
                                        <span>Account Settings</span>
                                    </a>
                                </div>
                            @endif
                        @endauth
                    @endif
                </div>

                <!-- Footer/Auth Section inside Drawer (guests only) -->
                @guest
                    <div class="p-5 border-t border-white/10 dark:border-slate-800 bg-[#0f172a]/20">
                        <div class="flex flex-col space-y-2.5">
                            <a href="{{ route('login') }}" @click="mobileMenuOpen = false"
                                class="block w-full px-4 py-2.5 rounded-xl border border-white/20 hover:border-white text-white hover:bg-white/10 font-bold text-center text-xs transition active:scale-95">
                                Login
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" @click="mobileMenuOpen = false"
                                    class="block w-full px-4 py-2.5 rounded-xl bg-white text-[#1e40af] hover:bg-blue-50 font-bold text-center text-xs transition shadow-md active:scale-95">
                                    Register
                                </a>
                            @endif
                        </div>
                    </div>
                @endguest

            </div>
        </div>
    </nav>

    @if (session('success') || session('error'))
        <div x-data="{ showFlashModal: true }" x-init="showFlashModal = true" x-show="showFlashModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <!-- Backdrop with modern blur -->
            <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-md transition-opacity duration-300"
                @click="showFlashModal = false"></div>

            <!-- Card Container -->
            <div class="relative bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl max-w-md w-full mx-4 border border-slate-150 dark:border-white/10 transform transition-all duration-300 z-50 p-8 sm:p-10 flex flex-col items-center gap-y-6"
                x-show="showFlashModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-8 scale-95">

                <!-- Header & Close Button Row -->
                <div class="flex items-start justify-between w-full gap-4">
                    <div class="flex flex-col gap-y-1">
                        @if (session('success'))
                            <span
                                class="text-[10px] font-black tracking-widest text-emerald-600 dark:text-emerald-400 uppercase font-display block">Notification</span>
                            <h3
                                class="text-base sm:text-lg font-extrabold text-slate-955 dark:text-white font-display tracking-tight leading-tight uppercase">
                                Success</h3>
                        @else
                            <span
                                class="text-[10px] font-black tracking-widest text-rose-600 dark:text-rose-400 uppercase font-display block">Notification</span>
                            <h3
                                class="text-base sm:text-lg font-extrabold text-slate-955 dark:text-white font-display tracking-tight leading-tight uppercase">
                                {{ str_contains(session('error'), 'Profiling') || str_contains(session('error'), 'profiling') || str_contains(session('error'), 'registry') ? 'Profile Incomplete' : 'Error' }}
                            </h3>
                        @endif
                    </div>
                    <button @click="showFlashModal = false" type="button"
                        class="p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 transition active:scale-95 shrink-0"
                        aria-label="Close dialog">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @if (session('success'))
                    <!-- Success Icon Box -->
                    <div
                        class="w-16 h-16 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-500/20 dark:border-emerald-500/30 shadow-lg shadow-emerald-500/5 relative shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span
                            class="absolute inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500 opacity-75 animate-ping -top-1 -right-1"></span>
                    </div>

                    <!-- Content -->
                    <p
                        class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed sm:leading-7 text-center w-full px-1">
                        {{ session('success') }}
                    </p>

                    <!-- Action Button -->
                    <button @click="showFlashModal = false"
                        class="w-full py-2.5 px-6 bg-emerald-600 hover:bg-emerald-500 dark:bg-emerald-500 dark:hover:bg-emerald-400 text-white font-bold text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/35 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200 cursor-pointer">
                        Okay
                    </button>
                @else
                    @if (str_contains(session('error'), 'Profiling') ||
                            str_contains(session('error'), 'profiling') ||
                            str_contains(session('error'), 'registry'))
                        <!-- Profile Incomplete / Friendly Invite Icon Box -->
                        <div
                            class="w-16 h-16 rounded-2xl bg-blue-500/10 dark:bg-blue-500/20 text-[#1e40af] dark:text-blue-400 flex items-center justify-center border border-blue-500/20 dark:border-blue-500/30 shadow-lg shadow-blue-500/5 relative shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>

                        <!-- Content -->
                        <p
                            class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed sm:leading-7 text-center w-full px-1">
                            {{ session('error') }}
                        </p>

                        <!-- Action Buttons -->
                        <div class="w-full flex flex-col gap-2">
                            <a href="{{ route('profile.profiling.create') }}"
                                class="w-full py-2.5 px-6 bg-[#1e40af] hover:bg-[#1e3a8a] text-white text-center font-bold text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-blue-500/20 hover:shadow-blue-500/35 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200 cursor-pointer block">
                                GO TO KK PROFILING
                            </a>
                            <button @click="showFlashModal = false"
                                class="w-full py-2.5 px-6 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs uppercase tracking-wider rounded-2xl active:scale-[0.98] transition-all duration-200 cursor-pointer">
                                Maybe Later
                            </button>
                        </div>
                    @else
                        <!-- Default Error Icon Box -->
                        <div
                            class="w-16 h-16 rounded-2xl bg-rose-500/10 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-500/20 dark:border-rose-500/30 shadow-lg shadow-rose-500/5 relative shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>

                        <!-- Content -->
                        <p
                            class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed sm:leading-7 text-center w-full px-1">
                            {{ session('error') }}
                        </p>

                        <!-- Action Button -->
                        <button @click="showFlashModal = false"
                            class="w-full py-2.5 px-6 bg-rose-600 hover:bg-rose-500 dark:bg-rose-500 dark:hover:bg-rose-400 text-white font-bold text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-rose-500/20 hover:shadow-rose-500/35 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.98] transition-all duration-200 cursor-pointer">
                            Close
                        </button>
                    @endif
                @endif
            </div>
        </div>
    @endif

    <!-- Main Content Slot -->
    <main class="flex-1 flex flex-col">
        @yield('content')
        {{ $slot ?? '' }}
    </main>

    <!-- Footer -->
    @unless ($hideFooter)
        <footer class="bg-slate-900 text-slate-400 py-10 md:py-12 border-t border-slate-800 text-xs mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <!-- Brand & Brief Mission Statement -->
                    <div class="text-center md:text-left space-y-2">
                        <span class="font-bold text-white tracking-wider font-display uppercase block text-sm">Sangguniang
                            Kabataan Namayan</span>
                        <p class="leading-relaxed text-slate-500 max-w-md text-xs">
                            Empowering youth governance in Barangay Namayan, Mandaluyong. Built with integrity,
                            transparency, and progress.
                        </p>
                    </div>

                    <!-- Social Icons and Quick Links -->
                    <div class="flex flex-col items-center md:items-end gap-2">
                        <span
                            class="font-bold text-slate-400 tracking-wider font-display uppercase block text-[10px]">CONNECT
                            WITH US</span>
                        <div class="flex items-center space-x-5 text-slate-500 text-base">
                            <!-- Facebook Icon -->
                            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer"
                                class="hover:text-white transition-colors duration-200" title="Facebook">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>
                            <!-- Instagram Icon -->
                            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer"
                                class="hover:text-white transition-colors duration-200" title="Instagram">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M12.315 2c2.43 0 2.784.01 3.71.054 1.14.051 1.96.23 2.65.5a4.877 4.877 0 011.77 1.15c.5.5.85 1.01 1.15 1.77.27.69.45 1.51.5 2.65.044.927.054 1.28.054 3.71 0 2.43-.01 2.784-.054 3.71-.051 1.14-.23 1.96-.5 2.65a4.877 4.877 0 01-1.15 1.77c-.5.5-1.01.85-1.77 1.15-.69.27-1.51.45-2.65.5-.927.044-1.28.054-3.71.054-2.43 0-2.784-.01-3.71-.054-1.14-.051-1.96-.23-2.65-.5a4.877 4.877 0 01-1.77-1.15c-.5-.5-.85-1.01-1.15-1.77-.27-.69-.45-1.51-.5-2.65C2.01 14.784 2 14.43 2 12c0-2.43.01-2.784.054-3.71.051-1.14.23-1.96.5-2.65a4.877 4.877 0 011.15-1.77c.5-.5 1.01-.85 1.77-1.15.69-.27 1.51-.45 2.65-.5C9.216 2.01 9.57 2 12 2zm0 1.8c-2.385 0-2.673.01-3.614.053-.873.04-1.347.185-1.662.308a3.076 3.076 0 00-1.143.744 3.076 3.076 0 00-.744 1.143c-.123.315-.268.789-.308 1.662C4.51 9.327 4.5 9.615 4.5 12c0 2.385.01 2.673.053 3.614.04.873.185 1.347.308 1.662.207.533.48 1.01.882 1.412a3.076 3.076 0 001.412.882c.315.123.789.268 1.662.308.941.043 1.229.053 3.614.053 2.385 0 2.673-.01 3.614-.053.873-.04 1.347-.185 1.662-.308a3.076 3.076 0 001.143-.744 3.076 3.076 0 00.744-1.143c.123-.315.268-.789.308-1.662.043-.941.053-1.229.053-3.614 0-2.385-.01-2.673-.053-3.614-.04-.873-.185-1.347-.308-1.662a3.076 3.076 0 00-.744-1.143 3.076 3.076 0 00-1.143-.744c-.315-.123-.789-.268-1.662-.308C14.673 3.81 14.385 3.8 12 3.8zm0 3.077A5.123 5.123 0 1012.001 17.15 5.123 5.123 0 0012 6.877zM12 15a3 3 0 110-6 3 3 0 010 6zm5.884-7.876a1.2 1.2 0 11-2.4 0 1.2 1.2 0 012.4 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>
                            <!-- Email/Envelope Icon -->
                            <a href="mailto:sknamayan@gmail.com" class="hover:text-white transition-colors duration-200"
                                title="Email Us">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-800 my-6">

                <div
                    class="flex flex-col sm:flex-row items-center justify-between gap-4 text-[10px] text-slate-600 uppercase tracking-widest">
                    <div>
                        &copy; {{ date('Y') }} SK Namayan. All rights reserved.
                    </div>
                    <div class="flex space-x-4">
                        @guest
                            <a href="{{ route('login') }}" class="hover:text-slate-400 transition-colors">Login</a>
                            <span>&middot;</span>
                            <a href="{{ route('register') }}" class="hover:text-slate-400 transition-colors">Register</a>
                        @else
                            <a href="{{ route('profile.edit') }}" class="hover:text-slate-400 transition-colors">My
                                Profile</a>
                        @endguest
                    </div>
                </div>
            </div>
        </footer>
    @endunless

    <!-- data-flash auto dismiss helper -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-flash]').forEach(function(el) {
                setTimeout(function() {
                    el.style.opacity = '0';
                    setTimeout(function() {
                        el.remove();
                    }, 300);
                }, 5000);
            });
        });
    </script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @auth
        @if (in_array(strtolower(auth()->user()->role), ['citizen', 'user']) &&
                !request()->is('admin*') &&
                !request()->routeIs('admin.*'))
            @include('citizen.skonsulta.floating-chat')
        @endif
    @endauth

    @livewireScripts
</body>

</html>
