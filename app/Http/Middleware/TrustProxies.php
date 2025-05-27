<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrustProxies extends Middleware
{
    /**
     * The proxies trusted by the application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*'; // Keep this as is

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, \Closure $next)
    {
        // Call the parent handle method first, which sets up trusted proxies
        $response = parent::handle($request, $next);

        // Retrieve and trim the X-Forwarded-Proto header
        $forwardedProto = trim($request->header('X-Forwarded-Proto'));
        $forwardedSsl = trim($request->header('X-Forwarded-Ssl')); // Also trim this for good measure

        // Explicitly force scheme if headers indicate HTTPS and Laravel still thinks it's HTTP
        if (($forwardedProto === 'https' || $forwardedSsl === 'on') && !$request->isSecure()) {
            $request->server->set('HTTPS', 'on'); // Set $_SERVER['HTTPS']
            $request->setScheme('https');         // Explicitly set the scheme on the Symfony Request object

            Log::info('TRUSTPROXIES: Explicitly forcing scheme to HTTPS.');
            Log::info('Request->isSecure() AFTER explicit forcing: ' . ($request->isSecure() ? 'true' : 'false'));
            Log::info('Request->getScheme() AFTER explicit forcing: ' . $request->getScheme());
        }

        Log::info('--- TrustProxies Debug End ---');

        return $response;
    }
}