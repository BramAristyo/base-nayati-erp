<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function showChangePasswordForm(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return redirect()->route('login');
            }

            return inertia('Utility/User/ChangePassword');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
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

        return redirect()->route('dashboard')->with('success', 'Password updated successfully.');
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

    public function showSettingForm(Request $request)
    {
        return inertia('Utility/User/Setting', [
            'user' => $request->user()
        ]);
    }

    public function updateSetting(Request $request)
    {
        $user = $request->user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ];

        if ($request->filled('password')) {
            $rules['current_password'] = ['required', 'current_password'];
            $rules['password'] = ['required', 'confirmed', 'min:8'];
        }

        $validated = $request->validate($rules);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
