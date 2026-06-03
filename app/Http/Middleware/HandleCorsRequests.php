<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleCorsRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log all CORS-relevant information for debugging
        if ($request->path() === 'checkout/generate-payment-token') {
            \Log::info('HandleCorsRequests middleware - Payment Token Request', [
                'method' => $request->method(),
                'path' => $request->path(),
                'origin' => $request->header('Origin'),
                'has_csrf_header' => $request->header('X-CSRF-TOKEN') ? 'YES' : 'NO',
                'has_session_cookie' => isset($_COOKIE[config('session.cookie')]) ? 'YES' : 'NO',
                'session_cookie_name' => config('session.cookie'),
                'all_cookies' => array_keys($_COOKIE),
            ]);
        }

        // Get origin from request
        $origin = $request->header('Origin');
        $allowedOrigins = [
            'https://deicide.my.id',
            'http://localhost',
            'http://localhost:8000',
            'http://localhost:3000',
            'http://127.0.0.1',
            'http://127.0.0.1:8000',
        ];

        // Check if origin is allowed (same-domain requests)
        $isAllowed = in_array($origin, $allowedOrigins) || empty($origin);

        if ($isAllowed) {
            $response = $next($request);

            // Add CORS headers for same-origin requests with credentials
            $response->header('Access-Control-Allow-Origin', $origin ?? '*');
            $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-CSRF-TOKEN, X-Requested-With');
            $response->header('Access-Control-Allow-Credentials', 'true');
            $response->header('Access-Control-Max-Age', '3600');

            return $response;
        }

        return $next($request);
    }
}
