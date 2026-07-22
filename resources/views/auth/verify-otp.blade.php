@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-950 font-sans flex items-center justify-center p-4 sm:p-6"
     x-data="{
         otp: '',
         loading: false,
         resending: false,
         errorMessage: '',
         successMessage: '',
         async verify() {
             if (this.otp.length !== 6) return;
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
                     body: JSON.stringify({ email: '{{ $email }}', otp: this.otp })
                 });
                 const data = await res.json();
                 if (res.ok && data.success) {
                     window.location.href = data.redirect_url;
                 } else {
                     this.errorMessage = data.message || 'Verification failed.';
                 }
             } catch (e) {
                 this.errorMessage = 'Network error. Please check your connection.';
             } finally {
                 this.loading = false;
             }
         },
         async resend() {
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
                 } else {
                     this.errorMessage = data.message || 'Resend failed.';
                 }
             } catch (e) {
                 this.errorMessage = 'Network error. Please try again.';
             } finally {
                 this.resending = false;
             }
         }
     }">
    <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6 shadow-2xl text-slate-100 animate-fade-in">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-blue-600/10 border border-blue-500/20 text-blue-400 flex items-center justify-center mx-auto text-xl font-bold">
                ✉
            </div>
            <h3 class="text-xl font-black font-display uppercase tracking-tight text-white">Email OTP Verification</h3>
            <p class="text-xs text-slate-400 leading-relaxed">
                We sent a 6-digit verification code to <span class="font-bold text-slate-200">{{ $email }}</span>. Enter it below to activate your account.
            </p>
        </div>

        @if(session('info'))
            <div class="p-3 bg-blue-950/40 border border-blue-900/30 rounded-2xl text-xs text-blue-400 font-medium text-center">
                {{ session('info') }}
            </div>
        @endif

        <div class="space-y-3">
            <input type="text" 
                   x-model="otp" 
                   maxlength="6" 
                   @input="if(otp.length === 6) verify()"
                   placeholder="000000"
                   class="w-full text-center tracking-[0.5em] font-mono text-2xl py-3.5 rounded-2xl border border-slate-700 bg-slate-955 text-white outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition font-bold">

            <template x-if="errorMessage">
                <div x-text="errorMessage" class="p-3 bg-rose-950/40 border border-rose-900/30 rounded-2xl text-xs font-bold text-rose-400 text-center"></div>
            </template>

            <template x-if="successMessage">
                <div x-text="successMessage" class="p-3 bg-emerald-950/40 border border-emerald-900/30 rounded-2xl text-xs font-bold text-emerald-400 text-center"></div>
            </template>
        </div>

        <button @click="verify()" 
                :disabled="otp.length !== 6 || loading"
                class="w-full py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white font-black text-xs uppercase tracking-wider transition active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer shadow-lg shadow-blue-600/20">
            <span x-text="loading ? 'Verifying...' : 'Verify Code & Activate'"></span>
        </button>

        <div class="pt-4 border-t border-slate-800 text-center">
            <p class="text-xs text-slate-400">
                Didn't receive code? 
                <button type="button" 
                        @click="resend()" 
                        :disabled="resending"
                        class="font-bold text-blue-400 hover:underline cursor-pointer disabled:opacity-50">
                    <span x-text="resending ? 'Sending...' : 'Click to Resend'"></span>
                </button>
            </p>
        </div>
    </div>
</div>
@endsection
