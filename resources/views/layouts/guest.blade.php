<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SK Namayan') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Fonts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 bg-[#f8fafc]">
        
        <div class="min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 bg-gradient-to-tr from-slate-900 via-[#1e3a8a] to-[#1e40af] relative overflow-hidden">
            <!-- Decorative light halos -->
            <div class="absolute w-[500px] h-[500px] rounded-full bg-blue-500/10 -top-40 -left-40 blur-3xl"></div>
            <div class="absolute w-[400px] h-[400px] rounded-full bg-indigo-500/10 -bottom-30 -right-30 blur-3xl"></div>

            <div class="w-full max-w-md relative z-10 space-y-6">
                <!-- Brand logo banner -->
                <div class="text-center">
                    <a href="/" class="inline-flex flex-col items-center space-y-2 group">
                        <img src="{{ asset('images/logo.png') }}" class="w-20 h-20 object-contain rounded-full bg-white p-1 border-2 border-blue-400 shadow-md group-hover:scale-105 transition duration-200" alt="SK Namayan Logo">
                        <h1 class="text-xl font-black font-display text-white tracking-wider uppercase">SK Namayan</h1>
                        <span class="text-[10px] font-bold tracking-[0.25em] text-blue-200 uppercase">Youth Government Portal</span>
                    </a>
                </div>

                <!-- Form Card wrapper -->
                <div class="bg-white/95 backdrop-blur-md px-6 py-8 sm:px-8 border border-white/10 rounded-3xl shadow-xl space-y-5">
                    {{ $slot }}
                </div>

                <!-- Footer back link -->
                <div class="text-center">
                    <a href="/" class="text-xs text-blue-200 hover:text-white transition font-medium">&larr; Back to Public Website</a>
                </div>
            </div>
        </div>

    </body>
</html>
