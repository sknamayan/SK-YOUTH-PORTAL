@extends('layouts.app')

@section('content')
<div class="min-h-[85vh] bg-slate-950 font-sans flex items-center justify-center p-4 sm:p-6 relative overflow-hidden"
     x-data="{
         otp: ['', '', '', '', '', ''],
         loading: false,
         resending: false,
         cooldown: 0,
         errorMessage: '',
         successMessage: '',
         get fullOtp() {
             return this.otp.join('');
         },
         handleInput(e, index) {
             const val = e.target.value;
             if (!/^\d*$/.test(val)) {
                 this.otp[index] = '';
                 return;
             }
             this.otp[index] = val.slice(-1);
             if (val && index < 5) {
                 this.$refs[`otp${index + 1}`].focus();
             }
             if (this.fullOtp.length === 6) {
                 this.verify();
             }
         },
         handleKeyDown(e, index) {
             if (e.key === 'Backspace' && !this.otp[index] && index > 0) {
                 this.$refs[`otp${index - 1}`].focus();
             }
         },
         handlePaste(e) {
             e.preventDefault();
             const pasted = (e.clipboardData || window.clipboardData).getData('text').trim();
             if (/^\d{6}$/.test(pasted)) {
                 this.otp = pasted.split('');
                 this.$refs.otp5.focus();
                 this.verify();
             }
         },
         async verify() {
             if (this.fullOtp.length !== 6) return;
             this.loading = true;
             this.errorMessage = '';
             this.successMessage = '';
             try {
                 const res = await fetch('{{ route('register.otp.verify') }}', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                     },
                     body: JSON.stringify({ email: '{{ $email }}', otp: this.fullOtp })
                 });
                 const data = await res.json();
                 if (res.ok && data.success) {
                     window.location.href = data.redirect_url;
                 } else {
                     this.errorMessage = data.message || 'Verification code failed.';
                 }
             } catch (e) {
                 this.errorMessage = 'Network error. Please check your connection.';
             } finally {
                 this.loading = false;
             }
         },
         async resend() {
             if (this.cooldown > 0 || this.resending) return;
             this.resending = true;
             this.errorMessage = '';
             this.successMessage = '';
             try {
                 const res = await fetch('{{ route('register.otp.resend') }}', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                     },
                     body: JSON.stringify({ email: '{{ $email }}' })
                 });
                 const data = await res.json();
                 if (res.ok && data.success) {
                     this.successMessage = data.message;
                     this.startCooldown(60);
                 } else {
                     this.errorMessage = data.message || 'Resend failed.';
                 }
             } catch (e) {
                 this.errorMessage = 'Network error. Please try again.';
             } finally {
                 this.resending = false;
             }
         },
         startCooldown(seconds) {
             this.cooldown = seconds;
             const timer = setInterval(() => {
                 this.cooldown--;
                 if (this.cooldown <= 0) clearInterval(timer);
             }, 1000);
         }
     }">
    
    <!-- Background Glow Effects -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-blue-600/10 blur-[120px] rounded-full pointer-events-none"></div>

    <div class="w-full max-w-lg bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-10 space-y-8 shadow-2xl backdrop-blur-xl relative z-10 text-slate-100 animate-fade-in">
        
        <!-- Header & Branding -->
        <div class="text-center space-y-3">
            <div class="inline-flex px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-black uppercase tracking-widest font-display">
                Barangay Namayan Youth Registry
            </div>
            <h2 class="text-2xl sm:text-3xl font-black font-display uppercase tracking-tight text-white">
                Verify Your Account
            </h2>
            <p class="text-xs text-slate-400 leading-relaxed max-w-sm mx-auto">
                We've sent a 6-digit security code to <strong class="text-slate-200 font-semibold">{{ $email }}</strong>. Please enter the code below to complete your registration.
            </p>
        </div>

        @if(session('info'))
            <div class="p-3.5 bg-blue-950/50 border border-blue-800/40 rounded-2xl text-xs text-blue-300 font-medium text-center flex items-center justify-center gap-2">
                <span>ℹ️</span> {{ session('info') }}
            </div>
        @endif

        <!-- 6 Digit Input Group -->
        <div class="space-y-4">
            <div class="flex justify-center items-center gap-2 sm:gap-3" @paste="handlePaste">
                <template x-for="(digit, i) in otp" :key="i">
                    <input type="text"
                           :x-ref="`otp${i}`"
                           maxlength="1"
                           x-model="otp[i]"
                           @input="handleInput($event, i)"
                           @keydown="handleKeyDown($event, i)"
                           class="w-11 h-14 sm:w-13 sm:h-16 text-center font-mono text-xl sm:text-2xl font-black rounded-2xl border border-slate-700 bg-slate-955 text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/15 transition shadow-inner">
                </template>
            </div>

            <!-- Error Notification -->
            <template x-if="errorMessage">
                <div x-text="errorMessage" class="p-3.5 bg-rose-950/50 border border-rose-900/50 rounded-2xl text-xs font-bold text-rose-400 text-center animate-shake"></div>
            </template>

            <!-- Success Notification -->
            <template x-if="successMessage">
                <div x-text="successMessage" class="p-3.5 bg-emerald-950/50 border border-emerald-900/50 rounded-2xl text-xs font-bold text-emerald-400 text-center"></div>
            </template>
        </div>

        <!-- Submit Button -->
        <button type="button" 
                @click="verify()" 
                :disabled="fullOtp.length !== 6 || loading"
                class="w-full py-4 rounded-2xl bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-black text-xs uppercase tracking-widest transition active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer shadow-lg shadow-blue-600/25 flex items-center justify-center gap-2">
            <span x-text="loading ? 'VERIFYING CODE...' : 'COMPLETE REGISTRATION'"></span>
            <span x-show="!loading" aria-hidden="true">&rarr;</span>
        </button>

        <!-- Resend Footer Link -->
        <div class="pt-4 border-t border-slate-800/80 text-center">
            <p class="text-xs text-slate-400">
                Didn't receive the email? 
                <button type="button" 
                        @click="resend()" 
                        :disabled="cooldown > 0 || resending"
                        class="font-black text-blue-400 hover:text-blue-300 hover:underline cursor-pointer disabled:opacity-50 disabled:hover:no-underline ml-1">
                    <span x-text="resending ? 'Sending Code...' : (cooldown > 0 ? `Resend Code in ${cooldown}s` : 'Resend Code')"></span>
                </button>
            </p>
        </div>
    </div>
</div>
@endsection
