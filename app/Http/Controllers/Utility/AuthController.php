<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return inertia('Utility/Auth/Login');
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            $remember = $request->boolean('remember');

            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();

                return redirect()->intended('/dashboard');
            }

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => $e->getMessage(),
            ]);
        }
    }

    public function me(Request $request)
    {
        try {

            $user = $request->user();

            if (!$user) {
                return $this->errorResponse('Unauthenticated.', 401);
            }

            $userId = $user->id;

            $roles = Cache::remember("user_roles_{$userId}", now()->addHour(), 
                fn () => $user->roles->pluck('name'));
                
            $permissions = Cache::remember("user_permissions_{$userId}", now()->addHour(),
                fn () => $user->getAllPermissions()->pluck('name'));


            return $this->successResponse([
                'user' => $user,
                'roles' => $roles,
                'permissions' => $permissions,
            ]);

        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }
}
