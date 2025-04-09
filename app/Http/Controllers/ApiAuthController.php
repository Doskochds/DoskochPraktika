<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\DTO\UserRegistrationDTO;
use App\DTO\UserLoginDTO;
use App\DTO\CurrentUserDTO;
use App\Services\AuthService;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

class ApiAuthController extends Controller
{
    protected AuthService $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Реєстрація користувача.
     */
    public function register(RegisterRequest $request)
    {
        $dto = new UserRegistrationDTO(
            $request->name,
            $request->email,
            $request->password
        );
        return $this->authService->register($dto);
    }
    /**
     * Логін користувача.
     */
    public function login(LoginRequest $request)
    {
        $dto = new UserLoginDTO(
            $request->email,
            $request->password
        );
        return $this->authService->login($dto);
    }
    /**
     * Логаут користувача (видалення всіх токенів).
     */
    public function logout(Request $request)
    {
        return $this->authService->logout($request);
    }
    /**
     * Повернення даних про поточного користувача.
     */
    public function user(Request $request)
    {
        $dto = new CurrentUserDTO($request->user()->email);
        return $this->authService->user($dto);
    }
}

