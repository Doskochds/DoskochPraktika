<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Реєстрація користувача.
     */
    public function register(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        $token = $user->createToken('auth_token')->plainTextToken;


        return response()->json(['token' => $token, 'user' => $user], 201);
    }

    /**
     * Логін користувача.
     */
    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);


        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }


        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token, 'user' => $user]);
    }

    /**
     * Логаут користувача (видалення всіх токенів).
     */
    public function logout(Request $request)
    {

        $request->user()->tokens()->delete();


        return response()->json(['message' => 'Logged out successfully']);
    }

    /**
     * Повернення даних про поточного користувача.
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
