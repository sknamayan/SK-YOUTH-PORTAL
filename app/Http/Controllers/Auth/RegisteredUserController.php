<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request and dispatch OTP code.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $otp = sprintf('%06d', random_int(0, 999999));

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'is_approved' => false,
            'otp_code' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(10),
            'otp_attempts' => 0,
        ]);

        // Link KkProfile if pre-existing
        $profile = \App\Models\KkProfile::withoutGlobalScopes()->where(function($query) use ($request) {
            $query->whereRaw('LOWER(first_name) = ?', [strtolower($request->first_name)])
                  ->whereRaw('LOWER(surname) = ?', [strtolower($request->last_name)])
                  ->whereDate('dob', $request->birthdate);
        })->whereNull('user_id')->first();

        if ($profile) {
            $profile->update([
                'user_id' => $user->id,
                'status' => 'approved',
            ]);
        }

        event(new Registered($user));

        try {
            $user->notify(new \App\Notifications\SendOtpNotification($otp));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed sending OTP notification: ' . $e->getMessage());
        }

        session(['pending_otp_email' => $user->email]);

        return redirect()->route('register.otp.prompt')
            ->with('info', 'Registration submitted! Please enter the 6-digit verification code sent to your email.');
    }

    /**
     * Display the OTP verification prompt page.
     */
    public function showOtpPrompt(): View|RedirectResponse
    {
        $email = session('pending_otp_email');
        if (!$email) {
            return redirect()->route('register');
        }

        return view('auth.verify-otp', compact('email'));
    }

    /**
     * Verify 6-digit numeric OTP code.
     */
    public function verifyOtp(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp'   => ['required', 'digits:6'],
        ]);

        $throttleKey = 'verify-otp:' . strtolower($request->email);

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return response()->json([
                'success' => false,
                'message' => "Too many verification attempts. Please try again in {$seconds} seconds.",
            ], 429);
        }

        $user = User::where('email', strtolower($request->email))->first();

        if (!$user || !$user->otp_code || !$user->otp_expires_at) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey);
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification session or expired code.',
            ], 422);
        }

        if (now()->isAfter($user->otp_expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired (10 min limit). Please click Resend.',
            ], 422);
        }

        if (!Hash::check($request->otp, $user->otp_code)) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey);
            $user->increment('otp_attempts');

            return response()->json([
                'success' => false,
                'message' => 'Incorrect verification code. Please check and try again.',
            ], 422);
        }

        \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);

        $user->update([
            'is_approved'    => true,
            'otp_code'       => null,
            'otp_expires_at' => null,
            'otp_attempts'   => 0,
        ]);

        Auth::login($user);
        session()->forget('pending_otp_email');

        return response()->json([
            'success'      => true,
            'message'      => 'Account verified successfully! Welcome to SK Namayan.',
            'redirect_url' => route('profile.my-requests'),
        ], 200);
    }

    /**
     * Resend a fresh 6-digit numeric OTP code.
     */
    public function resendOtp(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $throttleKey = 'resend-otp:' . strtolower($request->email);

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 1)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return response()->json([
                'success' => false,
                'message' => "Please wait {$seconds} seconds before requesting another code.",
            ], 429);
        }

        $user = User::where('email', strtolower($request->email))->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Account not found.'], 404);
        }

        $otp = sprintf('%06d', random_int(0, 999999));

        $user->update([
            'otp_code'       => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(10),
            'otp_attempts'   => 0,
        ]);

        try {
            $user->notify(new \App\Notifications\SendOtpNotification($otp));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed resending OTP notification: ' . $e->getMessage());
        }

        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);

        return response()->json([
            'success' => true,
            'message' => 'A fresh 6-digit verification code has been sent to your email.',
        ], 200);
    }
}
