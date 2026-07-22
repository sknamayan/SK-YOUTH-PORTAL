@extends('layouts.app')

@section('content')
<div class="min-h-[80vh] bg-slate-50 font-sans flex items-center justify-center p-4 sm:p-6"
     x-data="{
         otp: ['', '', '', '', '', ''],
         loading: false,
         resending: false,
         cooldown: 0,
         errorMessage: '{{ session('errorMessage') ?? '' }}',
         successMessage: '',
         get fullOtp() { return this.otp.join(''); },
         handleInput(e, i) {
             const val = e.target.value;
             if (!/^\d*$/.test(val)) { this.otp[i] = ''; return; }
             this.otp[i] = val.slice(-1);
             if (val && i < 5) { this.$refs[`otp${i + 1}`].focus(); }
             if (this.fullOtp.length === 6) { this.verify(); }
         },
         handleKeyDown(e, i) {
             if (e.key === 'Backspace' && !this.otp[i] && i > 0) {
                 this.$refs[`otp${i - 1}`].focus();
             }
         },
         async verify() {
             if (this.fullOtp.length !== 6) return;
             this.loading = true;
             this.errorMessage = '';
             try {
                 const res = await fetch('{{ route('register.otp.verify') }}', {
                     method: 'POST',
                     headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                     body: JSON.stringify({ email: '{{ $email }}', otp: this.fullOtp })
                 });
                 const data = await res.json();
                 if (res.ok && data.success) { window.location.href = data.redirect_url; }
                 else { this.errorMessage = data.message || 'Verification code failed.'; }
             } catch (e) { this.errorMessage = 'Network error. Please try again.'; }
             finally { this.loading = false; }
         },
         async resend() {
             if (this.cooldown > 0 || this.resending) return;
             this.resending = true;
             this.errorMessage = '';
             this.successMessage = '';
             try {
                 const res = await fetch('{{ route('register.otp.resend') }}', {
                     method: 'POST',
                     headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                     body: JSON.stringify({ email: '{{ $email }}' })
                 });
                 const data = await res.json();
                 if (res.ok && data.success) {
                     this.successMessage = data.message;
                     this.startCooldown(60);
                 } else { this.errorMessage = data.message || 'Resend failed.'; }
             } catch (e) { this.errorMessage = 'Network error. Please try again.'; }
             finally { this.resending = false; }
         },
         startCooldown(seconds) {
             this.cooldown = seconds;
             const timer = setInterval(() => {
                 this.cooldown--;
                 if (this.cooldown <= 0) clearInterval(timer);
             }, 1000);
         }
     }">

    <div class="w-full max-w-lg bg-white border border-slate-200 rounded-3xl p-6 sm:p-10 space-y-8 shadow-xl text-slate-800">
        
        <div class="text-center space-y-2">
            <div class="inline-flex px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-[10px] font-black uppercase tracking-widest font-display">
                Barangay Namayan Youth Registry
            </div>
            <h2 class="text-2xl sm:text-3xl font-black font-display uppercase tracking-tight text-slate-900">
                Verify Your Email
            </h2>
            <p class="text-xs text-slate-500 leading-relaxed max-w-sm mx-auto">
                Enter the 6-digit verification code sent to <strong class="text-slate-800 font-semibold">{{ $email }}</strong> to activate your account.
            </p>
        </div>

        @if(session('info'))
            <div class="p-3.5 bg-blue-50 border border-blue-200 rounded-2xl text-xs text-blue-700 font-semibold text-center">
                {{ session('info') }}
            </div>
        @endif

        <div class="space-y-4">
            <div class="flex justify-center items-center gap-2 sm:gap-3">
                <template x-for="(digit, i) in otp" :key="i">
                    <input type="text"
                           :x-ref="`otp${i}`"
                           maxlength="1"
                           x-model="otp[i]"
                           @input="handleInput($event, i)"
                           @keydown="handleKeyDown($event, i)"
                           class="w-11 h-14 sm:w-13 sm:h-16 text-center font-mono text-xl sm:text-2xl font-black rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 outline-none focus:border-blue-600 focus:bg-white focus:ring-4 focus:ring-blue-600/10 transition shadow-sm">
                </template>
            </div>

            <template x-if="errorMessage">
                <div x-text="errorMessage" class="p-3.5 bg-rose-50 border border-rose-200 rounded-2xl text-xs font-bold text-rose-600 text-center"></div>
            </template>

            <template x-if="successMessage">
                <div x-text="successMessage" class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs font-bold text-emerald-700 text-center"></div>
            </template>
        </div>

        <button type="button" 
                @click="verify()" 
                :disabled="fullOtp.length !== 6 || loading"
                class="w-full py-4 rounded-2xl bg-blue-700 hover:bg-blue-800 text-white font-black text-xs uppercase tracking-widest transition shadow-lg shadow-blue-700/20 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
            <span x-text="loading ? 'VERIFYING CODE...' : 'COMPLETE REGISTRATION &rarr;'"></span>
        </button>

        <div class="pt-4 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">
                Didn't receive the email code? 
                <button type="button" 
                        @click="resend()" 
                        :disabled="cooldown > 0 || resending"
                        class="font-bold text-blue-700 hover:underline cursor-pointer disabled:opacity-50 ml-1">
                    <span x-text="resending ? 'Sending Code...' : (cooldown > 0 ? `Resend Code in ${cooldown}s` : 'Resend Code')"></span>
                </button>
            </p>
        </div>
    </div>
</div>
@endsection
