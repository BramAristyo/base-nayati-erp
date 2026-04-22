<?php

namespace App\Http\Middleware\Utility;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->isPasswordChanged()) {
            if (!$request->is('change-password') && !$request->is('logout')) {
                return redirect()->route('user.change-password');
            }
        }

        if ($user && $user->isPasswordChanged() && $request->is('change-password')) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
