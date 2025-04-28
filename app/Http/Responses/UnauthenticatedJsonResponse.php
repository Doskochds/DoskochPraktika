<?php

namespace App\Http\Responses;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;

class UnauthenticatedJsonResponse implements Responsable
{
    public function toResponse($request)
    {
        return response()->json([
            'message' => 'Unauthenticated.',
        ], 401);
    }
}
