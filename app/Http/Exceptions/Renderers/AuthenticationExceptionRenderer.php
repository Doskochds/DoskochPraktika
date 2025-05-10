<?php

namespace App\Http\Exceptions\Renderers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Pennant\Middleware\EnsureHasFeature;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationExceptionRenderer
{
    public function __invoke(AuthenticationException $e, Request $request): Response
    {

        if ($request->expectsJson()) {
            return new JsonResponse([
                'message' => 'Unauthenticated.',
            ], 401);
        }


        return redirect()->guest(route('login'));
    }
}
