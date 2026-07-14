@props(['name'])

@switch($name)
    @case('education')
        <!-- Book -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
        @break

    @case('health')
        <!-- Heart -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
        </svg>
        @break

    @case('governance')
        <!-- Civic/Building -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
        </svg>
        @break

    @case('active-citizenship')
        <!-- User Group / Handshake -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        @break

    @case('social-inclusion')
        <!-- Social/Heart Scale -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        @break

    @case('peace-building')
        <!-- Shield -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
        </svg>
        @break

    @case('environment')
        <!-- Leaf -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 20A7 7 0 019.8 6.1C15.5 5 17 4.48 19 2c1 2 2 3.5 1 9.3a7 7 0 01-9 8.7zM9 22c1-3 4-6 10-10"></path>
        </svg>
        @break

    @case('youth-employment')
        <!-- Briefcase -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
        </svg>
        @break

    @case('agriculture')
        <!-- Seedling/Sprout -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10a6 6 0 00-6-6H4v2a6 6 0 006 6h2m0-2v10m0-10a6 6 0 016-6h2v2a6 6 0 01-6 6h-2"></path>
        </svg>
        @break

    @case('global-mobility')
        <!-- Globe -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 002 2h2a2.5 2.5 0 002.5-2.5V8a2 2 0 012-2h.068M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        @break

    @case('mental-health')
        <!-- Brain / Pulse -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364.364l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
        </svg>
        @break

    @case('sports')
        <!-- Trophy -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm0 13H9m3 0h3M5 7h14a2 2 0 012 2v2a7 7 0 01-14 0V9a2 2 0 012-2z"></path>
        </svg>
        @break

    @case('medicine')
        <!-- Pill / First-aid -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9h4m-2-2v4"></path>
        </svg>
        @break

    @case('track')
        <!-- Search -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        @break

    @case('dashboard')
        <!-- Chart -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm10 0V5a2 2 0 00-2-2h-2a2 2 0 00-2 2v16a2 2 0 002 2h2a2 2 0 002-2z"></path>
        </svg>
        @break

    @case('users')
        <!-- Users -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        @break

    @case('profile')
        <!-- Settings -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
        </svg>
        @break

    @case('website')
        <!-- Globe -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
        </svg>
        @break

    @case('pending')
        <!-- Clock -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        @break

    @case('logs')
        <!-- Clipboard List -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
        </svg>
        @break

    @case('carousel')
        <!-- Photo/Image Icon -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        @break

    @case('calendar')
        <!-- Calendar Days Icon -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"></path>
        </svg>
        @break

    @case('consultations')
        <!-- Chat Bubble Icon -->
        <svg {{ $attributes->merge(['class' => 'w-6 h-6', 'fill' => 'none', 'stroke' => 'currentColor', 'viewBox' => '0 0 24 24']) }} xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        @break
@endswitch
