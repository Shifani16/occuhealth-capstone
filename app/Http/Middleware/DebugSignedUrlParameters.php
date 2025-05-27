<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log; // Keep this here just in case logging starts working

class DebugSignedUrlParameters
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // IMPORTANT: This middleware is for temporary debugging ONLY.
        // It will stop the request and return JSON.
        // REMOVE THIS MIDDLEWARE AND ITS REGISTRATION AFTER DEBUGGING!

        // You can add a check here if needed, but for temporary debug,
        // we'll just dump the params for any route it's applied to.

        // Capture debug information
        $debugInfo = [
            'debug_signed_url_middleware_check' => true,
            'request_full_url_received_by_middleware' => $request->fullUrl(),
            'request_query_params_received_by_middleware' => $request->query->all(),
            'request_path' => $request->path(),
            'request_route_params' => $request->route() ? $request->route()->parameters() : 'No route matched yet',
            'is_secure_at_middleware' => $request->isSecure(),
            'root_url_at_middleware' => $request->root(),
            'x_forwarded_proto_header' => $request->header('X-Forwarded-Proto', 'NOT FOUND'),
            'message' => 'Temporary Debug Middleware: Inspect the query parameters above. Identify any unexpected parameters added by the proxy.',
            'NEXT_STEPS' => 'Add identified parameters to the $ignoreQuery array in URL::temporarySignedRoute. Then remove this middleware and its route registration.'
        ];

        // Log this info as well, just in case the logs suddenly appear
        Log::info('--- DebugSignedUrlParameters Middleware Hit ---');
        Log::info(json_encode($debugInfo));
        Log::info('---------------------------------------------');


        // *** TEMPORARY ACTION: Return the debug info as JSON ***
        // This bypasses the 'signed' middleware and prevents the 403,
        // allowing you to see the parameters received.
        return response()->json($debugInfo);

        // *** PERMANENT ACTION (after debugging): Uncomment the line below ***
        // return $next($request);
    }
}