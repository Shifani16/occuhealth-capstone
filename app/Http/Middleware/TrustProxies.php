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
        Log::info('TRUSTPROXIES: Starting handle method.');
        Log::info('TRUSTPROXIES: Request Scheme BEFORE parent::handle: ' . $request->getScheme());
        Log::info('TRUSTPROXIES: Request isSecure BEFORE parent::handle: ' . ($request->isSecure() ? 'true' : 'false'));
        Log::info('TRUSTPROXIES: X-Forwarded-Proto Header BEFORE parent::handle: ' . $request->header('X-Forwarded-Proto'));

        // Call the parent handle method first
        $response = parent::handle($request, $next);

        Log::info('TRUSTPROXIES: Request Scheme AFTER parent::handle: ' . $request->getScheme());
        Log::info('TRUSTPROXIES: Request isSecure AFTER parent::handle: ' . ($request->isSecure() ? 'true' : 'false'));

        $forwardedProto = trim($request->header('X-Forwarded-Proto'));
        $forwardedSsl = trim($request->header('X-Forwarded-Ssl'));

        Log::info('TRUSTPROXIES: Trimmed X-Forwarded-Proto: "' . $forwardedProto . '"');
        Log::info('TRUSTPROXIES: Trimmed X-Forwarded-Ssl: "' . $forwardedSsl . '"');

        if (($forwardedProto === 'https' || $forwardedSsl === 'on') && !$request->isSecure()) {
            Log::info('TRUSTPROXIES: Condition met: X-Forwarded-Proto is HTTPS and Request is not Secure. Forcing HTTPS.');
            $request->server->set('HTTPS', 'on');
            $request->setScheme('https');

            Log::info('TRUSTPROXIES: Request->isSecure() AFTER explicit forcing: ' . ($request->isSecure() ? 'true' : 'false'));
            Log::info('TRUSTPROXIES: Request->getScheme() AFTER explicit forcing: ' . $request->getScheme());
        } else {
            Log::info('TRUSTPROXIES: Condition NOT met for forcing HTTPS.');
            Log::info('TRUSTPROXIES: X-Forwarded-Proto check: ' . ($forwardedProto === 'https' ? 'true' : 'false'));
            Log::info('TRUSTPROXIES: X-Forwarded-Ssl check: ' . ($forwardedSsl === 'on' ? 'true' : 'false'));
            Log::info('TRUSTPROXIES: Request->isSecure() current status: ' . ($request->isSecure() ? 'true' : 'false'));
        }

        Log::info('--- TrustProxies Debug End ---');

        return $response;
    }
}