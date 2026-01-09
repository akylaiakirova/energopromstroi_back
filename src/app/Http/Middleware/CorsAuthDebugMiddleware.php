<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CorsAuthDebugMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $isAuthLogin = $request->is('api/auth/login');
        $isInterestingMethod = in_array($request->getMethod(), ['OPTIONS', 'POST'], true);

        if ($isAuthLogin && $isInterestingMethod) {
            Log::info('CORS auth/login request', [
                'method' => $request->getMethod(),
                'path' => $request->getPathInfo(),
                'origin' => $request->headers->get('Origin'),
                'access_control_request_method' => $request->headers->get('Access-Control-Request-Method'),
                'access_control_request_headers' => $request->headers->get('Access-Control-Request-Headers'),
                'host' => $request->getHost(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        /** @var Response $response */
        $response = $next($request);

        if ($isAuthLogin && $isInterestingMethod) {
            Log::info('CORS auth/login response', [
                'status' => $response->getStatusCode(),
                'acao' => $response->headers->get('Access-Control-Allow-Origin'),
                'acam' => $response->headers->get('Access-Control-Allow-Methods'),
                'acah' => $response->headers->get('Access-Control-Allow-Headers'),
                'acc' => $response->headers->get('Access-Control-Allow-Credentials'),
                'vary' => $response->headers->get('Vary'),
            ]);
        }

        return $response;
    }
}

