<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->header('Origin') ?: '*';

        // 1. Jika request adalah PREFLIGHT (OPTIONS), langsung kembalikan 200 OK dengan Header Lengkap
        if ($request->isMethod('OPTIONS')) {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $origin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin, Application')
                ->header('Access-Control-Allow-Credentials', 'true');
        }

        // 2. Jalankan request biasa (POST, GET, dll)
        $response = $next($request);

        // 3. Tempelkan Header CORS pada setiap respon balik
        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin, Application');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}