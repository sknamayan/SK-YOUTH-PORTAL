<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="space-y-1">
        <h2 class="text-lg font-bold text-slate-800 font-display uppercase tracking-tight">Citizen Login</h2>
        <p class="text-xs text-slate-400">Welcome back! Sign in to manage your active service requests.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4 pt-2">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="field focus:ring-4 focus:ring-blue-600/10">
            @error('email')
                <span class="text-rose-600 text-xs font-semibold mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <div x-data="{ showPassword: false }">
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-[10px] font-bold text-[#1e40af] hover:underline" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="relative">
                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" class="field focus:ring-4 focus:ring-blue-600/10 pr-10">
                <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-655 active:scale-95 transition focus:outline-none z-10">
                    <!-- Eye icon (show) -->
                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <!-- Eye off icon (hide) -->
                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                </button>
            </div>
            @error('password')
                <span class="text-rose-600 text-xs font-semibold mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <label for="remember_me" class="ml-2 text-xs font-semibold text-slate-500">Keep me logged in</label>
        </div>

        <div class="pt-2">
            <button type="submit" class="btn-primary w-full py-3">Sign In</button>
        </div>

        @if (Route::has('register'))
            <p class="text-center text-xs text-slate-400 pt-2">
                Don't have an account? 
                <a href="{{ route('register') }}" class="font-bold text-[#1e40af] hover:underline">Create Account</a>
            </p>
        @endif
    </form>
</x-guest-layout>
