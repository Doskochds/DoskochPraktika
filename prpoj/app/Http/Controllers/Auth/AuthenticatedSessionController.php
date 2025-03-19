<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;

class AuthenticatedSessionController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request)
    {
        return $this->authService->login($request);
    }

    public function destroy(Request $request)
    {
        return $this->authService->logout($request);
    }
}
