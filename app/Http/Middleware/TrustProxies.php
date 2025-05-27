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
    protected $proxies = '*';

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
        // Add X-Forwarded-SSL if your proxy might send it (less common than Proto)
        // | Request::HEADER_X_FORWARDED_SSL;


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle($request, \Closure $next)
    {
        // --- Debugging before TrustProxies parent logic ---
        Log::info('--- TrustProxies Debug Start ---');
        Log::info('Initial Request URI: ' . $request->getUri());
        Log::info('Initial request->isSecure(): ' . ($request->isSecure() ? 'true' : 'false'));
        Log::info('Initial request->root(): ' . $request->root());
        Log::info('Header X-Forwarded-Proto: ' . $request->header('X-Forwarded-Proto', 'NOT FOUND'));
        Log::info('Header X-Forwarded-Ssl: ' . $request->header('X-Forwarded-Ssl', 'NOT FOUND')); // Check this too
        Log::info('Request Scheme Before Parent: ' . $request->getScheme());
        // Log::info('All Request Headers: ' . json_encode($request->headers->all())); // Can be noisy


        // Call the parent handle method from Illuminate\Http\Middleware\TrustProxies.
        // This is where Symfony/Laravel's standard TrustProxies logic applies headers.
        $response = parent::handle($request, $next);

        // --- Debugging after TrustProxies parent logic ---
        Log::info('Request->isSecure() AFTER TrustProxies parent: ' . ($request->isSecure() ? 'true' : 'false'));
        Log::info('Request->root() AFTER TrustProxies parent: ' . $request->root());
        Log::info('Request->getScheme() AFTER TrustProxies parent: ' . $request->getScheme()); // Crucial check


        // --- Explicitly Force HTTPS Scheme IF TrustProxies Parent Didn't Do It Reliably ---
        // Check the X-Forwarded-Proto header AND confirm the request object isn't secure.
        // This handles scenarios where TrustProxies doesn't make isSecure(true) early enough or at all.
        $forwardedProto = $request->header('X-Forwarded-Proto');
        $forwardedSsl = $request->header('X-Forwarded-Ssl'); // Some proxies send this

        if (($forwardedProto === 'https' || $forwardedSsl === 'on') && !$request->isSecure()) {
             // If the header indicates HTTPS, but the request object isn't marked secure, force it.
             $request->server->set('HTTPS', 'on'); // Set $_SERVER['HTTPS'] which is often used by isSecure()
             $request->setScheme('https'); // Explicitly set the scheme on the Symfony Request object
             Log::info('TRUSTPROXIES: Explicitly forcing scheme to HTTPS.');
             Log::info('Request->isSecure() AFTER explicit forcing: ' . ($request->isSecure() ? 'true' : 'false'));
             Log::info('Request->getScheme() AFTER explicit forcing: ' . $request->getScheme());
        }
        // --- End Explicit Force ---

        Log::info('--- TrustProxies Debug End ---');

        return $response;
    }
}