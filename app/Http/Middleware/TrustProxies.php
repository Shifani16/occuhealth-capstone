<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 

class TrustProxies extends Middleware
{
    /**
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = '*'; 

    /**
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO;

    /**
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

        Log::info('Header X-Forwarded-Proto: ' . $request->header('X-Forwarded-Proto', 'NOT FOUND'));
        Log::info('Header X-Forwarded-Host: ' . $request->header('X-Forwarded-Host', 'NOT FOUND'));
        Log::info('Header X-Forwarded-For: ' . $request->header('X-Forwarded-For', 'NOT FOUND'));

        Log::info('All Request Headers: ' . json_encode($request->headers->all()));


        $response = parent::handle($request, $next);

        Log::info('Request->isSecure() AFTER TrustProxies logic: ' . ($request->isSecure() ? 'true' : 'false'));
        Log::info('Request->root() AFTER TrustProxies logic: ' . $request->root());
        Log::info('--- TrustProxies Debug End ---');

        return $response;
    }
}