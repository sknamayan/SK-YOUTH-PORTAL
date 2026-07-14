<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $scriptSrc = "'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com";
        $styleSrc = "'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net";
        $connectSrc = "'self'";
        $imgSrc = "'self' data: https:";

        // Allow Vite asset hosting and Hot Module Replacement WebSocket connection in local environment
        if (config('app.env') === 'local') {
            $scriptSrc .= " http://localhost:5173 http://127.0.0.1:5173 http://*:5173";
            $styleSrc .= " http://localhost:5173 http://127.0.0.1:5173 http://*:5173";
            $connectSrc .= " http://localhost:5173 http://127.0.0.1:5173 http://*:5173 ws://localhost:5173 ws://127.0.0.1:5173 ws://*:5173";
        }

        $csp = "default-src 'self'; " .
               "script-src {$scriptSrc}; " .
               "style-src {$styleSrc}; " .
               "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; " .
               "img-src {$imgSrc}; " .
               "connect-src {$connectSrc}; " .
               "frame-ancestors 'none'; " .
               "object-src 'none';";

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // Enforce HTTPS HSTS only in production environments
        if (config('app.env') === 'production') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
