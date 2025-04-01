<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\DTO\UserLoginDTO;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;

class AuthenticatedSessionController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Показати форму входу.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Обробка запиту на логін.
     *
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $dto = new UserLoginDTO(
            $request->input('email'),
            $request->input('password')
        );
        try {
            return $this->authService->login($dto);
        } catch (\Exception $e) {
            return Redirect::route('login')->withErrors(['email' => 'Невірні облікові дані.']);
        }
    }

    /**
     * Вихід з системи.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        // Викликаємо метод logout сервісу
        $this->authService->logout($request);

        // Переадресовуємо на сторінку логіну
        return redirect()->route('login');
    }
}
