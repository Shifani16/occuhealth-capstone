<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Add this line to use Log facade

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*'; // Ensure this is a single asterisk!

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
        Log::info('--- TrustProxies Debug Start ---');
        Log::info('Initial Request URI: ' . $request->getUri());
        Log::info('Initial request->isSecure(): ' . ($request->isSecure() ? 'true' : 'false'));
        Log::info('Initial request->root(): ' . $request->root());

        // Log specific X-Forwarded headers
        Log::info('Header X-Forwarded-Proto: ' . $request->header('X-Forwarded-Proto', 'NOT FOUND'));
        Log::info('Header X-Forwarded-Host: ' . $request->header('X-Forwarded-Host', 'NOT FOUND'));
        Log::info('Header X-Forwarded-For: ' . $request->header('X-Forwarded-For', 'NOT FOUND'));

        // Log ALL headers to catch anything unusual
        Log::info('All Request Headers: ' . json_encode($request->headers->all()));


        // Let the parent middleware (TrustProxies base class) handle the proxy logic
        $response = parent::handle($request, $next);

        Log::info('Request->isSecure() AFTER TrustProxies logic: ' . ($request->isSecure() ? 'true' : 'false'));
        Log::info('Request->root() AFTER TrustProxies logic: ' . $request->root());
        Log::info('--- TrustProxies Debug End ---');

        return $response;
    }
}