<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function __invoke(EmailVerificationRequest $request)
    {
        return $this->authService->verifyEmail(
            $request->user(),
            fn($user) => $user->markEmailAsVerified()
        );
    }
}
