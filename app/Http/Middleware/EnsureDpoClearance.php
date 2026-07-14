<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDpoClearance
{
    /**
     * Allow DPO, Admin, and Superadmin access (PII clearance holders).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->hasPiiClearance()) {
            return $next($request);
        }

        abort(403, 'Access Denied: DPO or Admin clearance required.');
    }
}
