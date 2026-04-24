<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BasicPaginateRequest;
use App\Http\Requests\Utility\Role\StoreRoleRequest;
use App\Http\Requests\Utility\Role\UpdateRoleRequest;
use App\Services\Utility\RolePermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Inertia\Response;

class RolePermissionController extends Controller
{
    // Administrator ID is 1. 
    // Can't update or delete the administrator role.
    private array $immutableRoleIds = [1];

    public function __construct(
        protected RolePermissionService $rolePermissionService
    ) {}

    #[Middleware('can:utility.role.view')]
    public function paginate(BasicPaginateRequest $request): Response|RedirectResponse
    {
        try {
            $roles = $this->rolePermissionService->paginate($request->validated());

            return inertia('Utility/RolePermission/Index', [
                'roles' => $roles,
                'filters' => $request->only(['search', 'sort_by', 'sort_order']),
            ]);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to load roles list.');
        }
    }

    #[Middleware('can:utility.role.create')]
    public function create(): Response|RedirectResponse
    {
        try {
            return inertia('Utility/RolePermission/Create');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to load create form.');
        }
    }

    #[Middleware('can:utility.role.create')]
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        try {
            $this->rolePermissionService->store($request->validated());

            return redirect()->route('utility.roles.paginate')->with('success', 'Role created successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to create role.')->withInput();
        }
    }

    #[Middleware('can:utility.role.view')]
    public function show(int $id): Response|RedirectResponse
    {
        try {
            $role = $this->rolePermissionService->find($id);

            return inertia('Utility/RolePermission/Show', ['role' => $role]);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to load role details.');
        }
    }

    #[Middleware('can:utility.role.edit')]
    public function update(UpdateRoleRequest $request, int $id): RedirectResponse
    {
        if (in_array($id, $this->immutableRoleIds)) {
            return back()->with('error', 'System roles cannot be modified.');
        }

        try {
            $this->rolePermissionService->update($id, $request->validated());

            return redirect()->route('utility.roles.paginate')->with('success', 'Role updated successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update role.');
        }
    }

    #[Middleware('can:utility.role.delete')]
    public function delete(int $id): RedirectResponse
    {
        if (in_array($id, $this->immutableRoleIds)) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        try {
            $this->rolePermissionService->delete($id);

            return redirect()->route('utility.roles.paginate')->with('success', 'Role deleted successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to delete role.');
        }
    }

    public function getAll(): JsonResponse
    {
        try {
            return $this->successResponse($this->rolePermissionService->getAll(), 'Roles fetched successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return $this->errorResponse('Failed to fetch roles.', 500);
        }
    }

    public function permissions(): JsonResponse
    {
        try {
            return $this->successResponse($this->rolePermissionService->permissions(), 'Permissions fetched successfully.');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return $this->errorResponse('Failed to fetch permissions.', 500);
        }
    }
}
