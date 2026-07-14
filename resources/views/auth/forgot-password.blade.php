<x-guest-layout>
    <div class="space-y-1">
        <h2 class="text-lg font-bold text-slate-800 font-display uppercase tracking-tight">Reset Password</h2>
        <p class="text-xs text-slate-400">Forgot your password? Enter your email address and we will mail you a reset link.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4 pt-2">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="field focus:ring-4 focus:ring-blue-600/10" placeholder="e.g. user@example.com">
            @error('email')
                <span class="text-rose-600 text-xs font-semibold mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <div class="pt-2">
            <button type="submit" class="btn-primary w-full py-3">Email Password Reset Link</button>
        </div>

        <p class="text-center text-xs text-slate-400 pt-2">
            Remembered your credentials? 
            <a href="{{ route('login') }}" class="font-bold text-[#1e40af] hover:underline">Sign In</a>
        </p>
    </form>
</x-guest-layout>
