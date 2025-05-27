<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class DebugSignedUrlParameters
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check the X-Forwarded-Proto header as the authoritative source for the original scheme
        if ($request->header('X-Forwarded-Proto') === 'https') {
            // Explicitly force the request object's scheme to HTTPS
            // This should make request->isSecure() and request->getScheme() return true/https
            // This is often necessary when TrustProxies doesn't make isSecure(true) early enough
            $request->server->set('HTTPS', 'on'); // Set the $_SERVER['HTTPS'] variable
            $request->setScheme('https'); // Explicitly set the scheme on the Symfony Request object
        }

        // Optional: Add logging here *after* attempting to force scheme, before 'signed' middleware runs
        Log::info('--- DebugSignedUrlParameters Middleware State Before Next ---');
        Log::info('is_secure AFTER forcing: ' . ($request->isSecure() ? 'true' : 'false'));
        Log::info('Scheme AFTER forcing: ' . $request->getScheme());
        Log::info('Full URL AFTER forcing: ' . $request->fullUrl()); // Check if this shows https now
        Log::info('---------------------------------------------------------');


        // Allow the request to proceed to the next middleware (which is 'signed')
        // *** THIS IS THE KEY CHANGE - NO LONGER RETURNING JSON ***
        return $next($request);
    }
}