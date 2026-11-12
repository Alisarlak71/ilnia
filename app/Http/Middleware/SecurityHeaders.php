<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Basic browser security headers on every web response:
 * - the site cannot be shown inside another site's frame (clickjacking),
 * - browsers must not guess file types,
 * - other sites only learn our domain from links, not full URLs (order numbers stay private),
 * - camera / microphone / location are never used,
 * - over HTTPS: browsers remember to always use HTTPS (HSTS).
 *
 * No Content-Security-Policy yet: Livewire/Alpine need inline scripts and a wrong CSP would
 * break checkout. Revisit after launch.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = $response->headers;
        $headers->set('X-Frame-Options', 'SAMEORIGIN');
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if ($request->isSecure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        return $response;
    }
}
