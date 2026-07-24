<x-guest-layout>
    <div class="space-y-1">
        <h2 class="text-lg font-bold text-slate-800 font-display uppercase tracking-tight">Create Account</h2>
        <p class="text-xs text-slate-400">Join the SK Portal to easily file and track your service requests.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4 pt-2" x-data="{ showPassword: false }">
        @csrf

        <!-- First & Last Name -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="first_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">First Name</label>
                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus autocomplete="given-name" class="field focus:ring-4 focus:ring-blue-600/10" placeholder="e.g. Neil">
                @error('first_name')
                    <span class="text-rose-600 text-xs font-semibold mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="last_name" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Last Name</label>
                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name" class="field focus:ring-4 focus:ring-blue-600/10" placeholder="e.g. Osorio">
                @error('last_name')
                    <span class="text-rose-600 text-xs font-semibold mt-1 block">{{ $message }}</span>
                @enderror
            </div>
        </div>
 
        <!-- Date of Birth -->
        <div>
            <label for="birthdate" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Date of Birth</label>
            <input id="birthdate" type="date" name="birthdate" value="{{ old('birthdate') }}" required class="field focus:ring-4 focus:ring-blue-600/10 dark:text-white">
            @error('birthdate')
                <span class="text-rose-600 text-xs font-semibold mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="field focus:ring-4 focus:ring-blue-600/10" placeholder="e.g. user@example.com">
            @error('email')
                <span class="text-rose-600 text-xs font-semibold mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Password</label>
            <div class="relative">
                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="new-password" class="field focus:ring-4 focus:ring-blue-600/10 pr-10">
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

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Confirm Password</label>
            <div class="relative">
                <input id="password_confirmation" :type="showPassword ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" class="field focus:ring-4 focus:ring-blue-600/10 pr-10">
                <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-655 active:scale-95 transition focus:outline-none z-10">
                    <!-- Eye icon (show) -->
                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    <!-- Eye off icon (hide) -->
                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                </button>
            </div>
            @error('password_confirmation')
                <span class="text-rose-600 text-xs font-semibold mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <div class="pt-2">
            <button type="submit" class="btn-primary w-full py-3">Register Account</button>
        </div>

        <p class="text-center text-xs text-slate-400 pt-2">
            Already registered? 
            <a href="{{ route('login') }}" class="font-bold text-[#1e40af] hover:underline">Sign In</a>
        </p>
    </form>
</x-guest-layout>
