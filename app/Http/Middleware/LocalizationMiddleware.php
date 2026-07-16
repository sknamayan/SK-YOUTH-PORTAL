<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $userLocale = auth()->user()->language;
            if (in_array($userLocale, ['en', 'fil'])) {
                app()->setLocale($userLocale);
            }
        } elseif (session()->has('locale')) {
            $sessionLocale = session()->get('locale');
            if (in_array($sessionLocale, ['en', 'fil'])) {
                app()->setLocale($sessionLocale);
            }
        }

        return $next($request);
    }
}
