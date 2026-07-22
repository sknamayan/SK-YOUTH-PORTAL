<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOtpVerified
{
    /**
     * Handle an incoming request.
     * Intercepts authenticated users whose accounts are not yet OTP-verified / approved.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // If user logged in but account is unapproved / unverified
            if (!$user->is_approved) {
                // Store email in session to allow verification prompt rendering
                session(['pending_otp_email' => $user->email]);

                // Logout session to enforce security boundary
                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Your account requires email OTP verification before proceeding.',
                        'redirect_url' => route('register.otp.prompt'),
                    ], 403);
                }

                return redirect()->route('register.otp.prompt')
                    ->with('info', 'Please verify your email address with the 6-digit OTP code sent to your inbox.');
            }
        }

        return $next($request);
    }
}
