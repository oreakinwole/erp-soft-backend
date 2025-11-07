<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\JsonResponse;

class Authenticate extends Middleware
{
    /**
     * Handle unauthenticated requests.
     */
    protected function unauthenticated($request, array $guards)
    {
        // Instead of redirecting, return JSON response
        abort(response()->json([
            'status'  => 'error',
            'message' => 'Unauthenticated',
            'data'    => []
        ], JsonResponse::HTTP_UNAUTHORIZED));
    }

    /**
     * Disable redirecting to a login route
     */
    protected function redirectTo($request): ?string
    {
        return null;
    }
}
