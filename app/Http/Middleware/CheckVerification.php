<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Skip if no user is logged in or if user is an admin
        if (!$user || $user->hasRole(['admin', 'ops', 'csr', 'content'])) {
            return $next($request);
        }

        // Skip if user has already completed verification or chosen to skip
        if (!$user->needsVerification()) {
            return $next($request);
        }

        // Skip if already on verification routes
        if ($request->is('verification/*') || $request->is('logout') || $request->is('account/verification*')) {
            return $next($request);
        }

        // Redirect to verification page
        return redirect()->route('verification.notice');
    }
}
