<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BasicPaginateRequest;
use App\Models\Utility\Role;
use App\Models\Utility\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function showChangePasswordForm(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return redirect()->route('login');
            }

            return inertia('Utility/Auth/ChangePassword');
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
        return inertia('Utility/Auth/Setting', [
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

    #[Middleware('can:utility.user.view')]
    public function paginate(BasicPaginateRequest $request)
    {
        try {
            $users = User::query()
                ->search($request->search)
                ->orderBy($request->sort_by, $request->sort_order)
                ->paginate($request->per_page)
                ->withQueryString();

            return inertia('Utility/User/Index', [
                'users' => $users,
                'filters' => [
                    'search' => $request->search,
                    'sortField' => $request->input('sortField', 'name'),
                    'sortOrder' => (int) $request->input('sortOrder', 1),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Error while loading user data.');
        }
    }

    #[Middleware('can:utility.user.create')]
    public function create()
    {
        try {
            return inertia('Utility/User/Create');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Error while loading user data.');
        }
    }

    #[Middleware('can:utility.user.create')]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'approver_name' => 'string|max:255',
            'approver_title' => 'string|max:255',
            'branch_code' => 'required|string|max:5',
            'position' => 'required|string|max:255',
            'is_active' => 'required|boolean',
            'roles' => 'required|array',
            'warehouses' => 'array',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'approver_name' => $validated['approver_name'],
                'approver_title' => $validated['approver_title'],
                'branch_code' => $validated['branch_code'],
                'position' => $validated['position'],
                'is_active' => $validated['is_active'],
                'password' => Hash::make('password'),
                'is_password_changed' => false,
            ]);

            $user->roles()->sync(Role::whereIn('slug', $validated['roles'])->pluck('id'));
            $user->warehouses()->sync($validated['warehouses']);

            return redirect()->route('utility.users.paginate')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to create user.')->withInput();
        }
    }

    #[Middleware('can:utility.user.view')]
    public function show($id)
    {
        try {
            $user = User::with(['roles', 'warehouses', 'permissions'])->findOrFail($id);
            return inertia('Utility/User/Show', [
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Error while loading user data.');
        }
    }

    #[Middleware('can:utility.user.edit')]
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'approver_name' => 'string|max:255',
            'approver_title' => 'string|max:255',
            'branch_code' => 'required|string|max:50',
            'position' => 'required|string|max:255',
            'is_active' => 'required|boolean',
            'roles' => 'required|array',
            'warehouses' => 'array',
        ]);

        try {
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'approver_name' => $validated['approver_name'],
                'approver_title' => $validated['approver_title'],
                'branch_code' => $validated['branch_code'],
                'position' => $validated['position'],
                'is_active' => $validated['is_active'],
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            $user->roles()->sync(Role::whereIn('slug', $validated['roles'])->pluck('id'));
            $user->warehouses()->sync($validated['warehouses']);

            return redirect()->route('utility.users.paginate')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update user.');
        }
    }

}
