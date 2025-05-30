<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowAllCors
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:4000');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-XSRF-TOKEN');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        if ($request->getMethod() === "OPTIONS") {
            $response->setStatusCode(204);
        }

        return $response;
    }
}
