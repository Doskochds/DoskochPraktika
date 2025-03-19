<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function update(Request $request)
    {
        $validated = $request->validateWithBag(
            'updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', \Illuminate\Validation\Rules\Password::defaults(), 'confirmed'],
            ]
        );

        return $this->authService->updatePassword($request->user(), $validated);
    }
}
