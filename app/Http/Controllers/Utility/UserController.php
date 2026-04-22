<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function showChangePasswordForm(Request $request)
    {
        try {

            $user = $request->user();

            if (!$user) {
                return $this->errorResponse('Unauthenticated.', 401);
            }

            return inertia('Utility/User/ChangePassword');

        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $credentials = $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
            'password_confirmation' => ['required'],
        ]);

        $user->password = bcrypt($credentials['password']);
        $user->is_password_changed = true;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Password changed successfully.');
    }

    public function isPasswordChanged(Request $request)
    {
        try {

            $user = $request->user();

            if (!$user) {
                return $this->errorResponse('Unauthenticated.', 401);
            }

            return $this->successResponse([
                'is_password_changed' => $user->isPasswordChanged(),
            ]);

        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }
}
