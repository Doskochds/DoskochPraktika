<?php

declare(strict_types=1);
namespace App\Services;

use App\DTO\PasswordResetDTO;
use App\DTO\PasswordUpdateDTO;
use App\DTO\UserRegistrationDTO;
use App\DTO\UserLoginDTO;
use App\DTO\CurrentUserDTO;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthService
{
    /**
     * Handle email verification actions.
     */
    public function sendVerificationNotification(User $user): RedirectResponse
    {
        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }
        $user->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    }

    public function handleVerificationPrompt(User $user): RedirectResponse
    {
        return $user->hasVerifiedEmail()
            ? redirect()->intended(route('dashboard', absolute: false))
            : view('auth.verify-email');
    }

    public function verifyEmail(User $user, callable $callback): RedirectResponse
    {
        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
        }
        if ($callback($user)) {
            event(new Verified($user));
        }
        return redirect()->intended(route('dashboard', absolute: false) . '?verified=1');
    }

    /**
     * Handle password-related actions.
     */
    public function updatePassword(User $user, PasswordUpdateDTO $dto): RedirectResponse
    {
        $user->update(
            [
                'password' => Hash::make($dto->password),
            ]
        );
        return back()->with('status', 'password-updated');
    }

    public function sendPasswordResetLink(PasswordResetDTO $dto): RedirectResponse
    {
        $status = Password::sendResetLink(['email' => $dto->email]);

        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(PasswordResetDTO $dto, callable $callback): RedirectResponse
    {
        $status = Password::reset(
            ['email' => $dto->email, 'token' => $dto->token, 'password' => $dto->password],
            $callback
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withInput()->withErrors(['email' => __($status)]);
    }

    public function confirmPassword(User $user, string $password): RedirectResponse
    {
        if (! Auth::guard('web')->validate(
            [
                'email' => $user->email,
                'password' => $password,
            ]
        )) {
            throw ValidationException::withMessages(
                [
                    'password' => __('auth.password'),
                ]
            );
        }
        session()->put('auth.password_confirmed_at', time());
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Handle authentication actions.
     */
    public function login(UserLoginDTO $dto): RedirectResponse|JsonResponse
    {
        $user = User::where('email', $dto->email)->first();
        if (!$user || !Hash::check($dto->password, $user->password)) {
            if (request()->wantsJson()) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
            return redirect()->route('login')->withErrors(['email' => 'Невірні облікові дані.']);
        }
        session()->invalidate();
        session()->regenerateToken();
        Auth::login($user, true);
        if (request()->wantsJson()) {
            return response()->json(['message' => 'Login successful', 'user' => $user]);
        }
        return redirect()->route('dashboard');
    }


    public function logout(\Illuminate\Http\Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function register(UserRegistrationDTO $dto): RedirectResponse|JsonResponse
    {
        $user = User::create(
            [
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => Hash::make($dto->password),
            ]
        );

        event(new Registered($user));

        Auth::login($user);
        if (request()->wantsJson()) {
            return response()->json(['message' => 'Register successful', 'user' => $user]);
        }
        return redirect(route('dashboard', absolute: false));
    }

    public function user(CurrentUserDTO $dto)
    {
        $user = User::where('email', $dto->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json(['user' => $user]);
    }
}

