<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
    public function updatePassword(User $user, array $validated): RedirectResponse
    {
        $user->update(
            [
                'password' => Hash::make($validated['password']),
            ]
        );

        return back()->with('status', 'password-updated');
    }

    public function sendPasswordResetLink(array $validated): RedirectResponse
    {
        $status = Password::sendResetLink($validated);

        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(array $validated, callable $callback): RedirectResponse
    {
        $status = Password::reset($validated, $callback);

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
    public function login($request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function logout($request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function register(array $validated): RedirectResponse
    {
        $user = User::create(
            [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]
        );

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}

