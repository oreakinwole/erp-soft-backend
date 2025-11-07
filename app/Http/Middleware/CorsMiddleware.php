<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Handle preflight OPTIONS request
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $this->getAllowedOrigin($request))
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin, ngrok-skip-browser-warning')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '86400');
        }

        // Handle actual request
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', $this->getAllowedOrigin($request));
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin, ngrok-skip-browser-warning');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        return $response;
    }

    /**
     * Get the allowed origin based on the request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    private function getAllowedOrigin(Request $request)
    {
        $origin = $request->headers->get('Origin');

        // List of allowed origins
        $allowedOrigins = [
            'http://localhost:3000',
            'http://localhost:3001',
            'http://127.0.0.1:3000',
            'http://127.0.0.1:3001',
        ];

        // Allow any origin that matches these patterns
        if ($origin) {
            // Allow localhost on any port
            if (str_starts_with($origin, 'http://localhost') ||
                str_starts_with($origin, 'https://localhost') ||
                str_starts_with($origin, 'http://127.0.0.1') ||
                str_starts_with($origin, 'https://127.0.0.1')) {
                return $origin;
            }

            // Allow Netlify domains (*.netlify.app)
            if (str_ends_with(parse_url($origin, PHP_URL_HOST) ?? '', '.netlify.app')) {
                return $origin;
            }

            // Check if origin is in allowed list
            if (in_array($origin, $allowedOrigins)) {
                return $origin;
            }
        }

        // Default to wildcard for any other origin
        return '*';
    }
}
