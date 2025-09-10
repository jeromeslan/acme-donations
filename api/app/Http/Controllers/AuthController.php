<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);
        if (! Auth::attempt($credentials, true)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }
        $request->session()->regenerate();
        return response()->noContent();
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->noContent();
    }

    public function me(Request $request)
    {
        $user = $request->user();
        if (! $user) return response()->json(null, 204);
        $user->load('roles');
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
        ]);
    }
}


