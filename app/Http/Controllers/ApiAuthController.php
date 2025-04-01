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
        // Створюємо DTO для реєстрації
        $dto = new UserRegistrationDTO(
            $request->name,
            $request->email,
            $request->password
        );

        // Викликаємо сервіс для реєстрації
        return $this->authService->register($dto);
    }

    /**
     * Логін користувача.
     */
    public function login(LoginRequest $request)
    {
        // Створюємо DTO для логіну
        $dto = new UserLoginDTO(
            $request->email,
            $request->password
        );

        // Викликаємо сервіс для логіну
        return $this->authService->login($dto);
    }

    /**
     * Логаут користувача (видалення всіх токенів).
     */
    public function logout(Request $request)
    {
        // Викликаємо сервіс для логауту
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

