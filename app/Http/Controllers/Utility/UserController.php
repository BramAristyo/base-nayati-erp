<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BasicPaginateRequest;
use App\Http\Requests\Utility\User\ChangePasswordRequest;
use App\Http\Requests\Utility\User\StoreUserRequest;
use App\Http\Requests\Utility\User\UpdateSettingRequest;
use App\Http\Requests\Utility\User\UpdateUserRequest;
use App\Models\Utility\User;
use App\Services\Utility\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    #[Middleware('can:utility.user.view')]
    public function paginate(BasicPaginateRequest $request): Response|RedirectResponse
    {
        try {
            $users = $this->userService->paginate($request->validated());

            return inertia('Utility/User/Index', [
                'users' => $users,
                'filters' => $request->only(['search', 'sort_by', 'sort_order']),
            ]);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to load user list.');
        }
    }

    #[Middleware('can:utility.user.create')]
    public function create(): Response|RedirectResponse
    {
        try {
            return inertia('Utility/User/Create');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to load create form.');
        }
    }

    
    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            $this->userService->store($request->validated());

            return redirect()->route('utility.users.paginate')->with('success', 'User created successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to create user.')->withInput();
        }
    }

    public function show(int $id): Response|RedirectResponse
    {
        try {
            $user = User::with(['roles', 'warehouses'])->findOrFail($id);

            return inertia('Utility/User/Show', [
                'user' => $user,
            ]);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to load user details.');
        }
    }

    public function update(UpdateUserRequest $request, int $id): RedirectResponse
    {
        try {
            $this->userService->update($id, $request->validated());

            return redirect()->route('utility.users.paginate')->with('success', 'User updated successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update user.');
        }
    }

    public function isPasswordChanged(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return $this->errorResponse('Unauthenticated.', 401);
            }

            return $this->successResponse(['is_password_changed' => (bool) $user->is_password_changed]);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return $this->errorResponse('Failed to verify password status.', 500);
        }
    }

    public function showChangePasswordForm(): Response|RedirectResponse
    {
        try {
            return inertia('Utility/Auth/ChangePassword');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to load change password form.');
        }
    }

    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        try {
            $this->userService->updatePassword($request->user(), $request->password);

            return redirect()->route('dashboard')->with('success', 'Password updated successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update password.');
        }
    }

    public function showSettingForm(Request $request): Response|RedirectResponse
    {
        try {
            return inertia('Utility/Auth/Setting', ['user' => $request->user()]);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to load setting form.');
        }
    }

    public function updateSetting(UpdateSettingRequest $request): RedirectResponse
    {
        try {
            $user = $request->user();

            $this->userService->update($user->id, array_merge($request->validated(), [
                'branch_code' => $user->branch_code,
                'position' => $user->position,
                'is_active' => $user->is_active,
                'roles' => $user->roles->pluck('slug')->toArray()
            ]));

            return back()->with('success', 'Profile updated successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update settings.');
        }
    }
}
