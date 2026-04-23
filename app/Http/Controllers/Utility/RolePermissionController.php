<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BasicPaginateRequest;
use App\Models\Utility\Permission;
use App\Models\Utility\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Attributes\Controllers\Middleware;

class RolePermissionController extends Controller
{
    private array $defaultIds = [1,2];
    
    #[Middleware('can:utility.role.view')]
    public function getAllRoles()
    {
        try {
            $roles = Role::all();

            return $this->successResponse(
                $roles,
                'Roles fetched successfully',
                200
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->errorResponse('Failed to fetch roles', 500);
        }
    }

    #[Middleware('can:utility.role.view')]
    public function getAllPermissions()
    {
        try {
            $permissions = Permission::all();

            return $this->successResponse(
                $permissions,
                'Permissions fetched successfully',
                200
            );
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $this->errorResponse('Failed to fetch permissions', 500);
        }
    }

    #[Middleware('can:utility.role.view')]
    public function paginate(BasicPaginateRequest $request)
    {
        try {
            $roles = Role::query()
                ->withCount('permissions')
                ->when($request->search, function ($query) use ($request) {
                    $query->where('name', 'like', "%{$request->search}%")
                          ->orWhere('slug', 'like', "%{$request->search}%");
                })
                ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc')
                ->paginate($request->per_page)
                ->withQueryString();

            return inertia('Utility/RolePermission/Index', [
                'roles' => $roles,
                'filters' => [
                    'search' => $request->search,
                    'sortField' => $request->input('sortField', 'created_at'),
                    'sortOrder' => (int) $request->input('sortOrder', 1),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Error while loading roles.');
        }
    }

    #[Middleware('can:utility.role.create')]
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:roles,slug',
                'description' => 'nullable|string',
                'permission_ids' => 'required|array',
                'permission_ids.*' => 'exists:permissions,id',
            ]);

            $role = Role::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'],
            ]);

            $role->permissions()->sync($validated['permission_ids']);

            return redirect()->route('utility.roles.paginate')->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to create role.')->withInput();
        }
    }

    #[Middleware('can:utility.role.create')]
    public function create()
    {
        try {
            return inertia('Utility/RolePermission/Create');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Error while loading roles.');
        }
    }

    #[Middleware('can:utility.role.view')]
    public function show($id)
    {
        try {
            $role = Role::query()
                ->with('permissions')
                ->findOrFail($id);

            return inertia('Utility/RolePermission/Show', [
                'role' => $role
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Error while loading role.');
        }
    }

    #[Middleware('can:utility.role.edit')]
    public function update(Request $request, $id)
    {
        if (in_array((int) $id, $this->defaultIds)) {
            return back()->with('error', 'Default roles cannot be modified.');
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:roles,slug,' . $id,
                'description' => 'nullable|string',
                'permission_ids' => 'required|array',
                'permission_ids.*' => 'exists:permissions,id',
            ]);

            $role = Role::findOrFail($id);
            $role->update([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'],
            ]);

            // Sync by ID
            $role->permissions()->sync($validated['permission_ids']);

            return redirect()->route('utility.roles.paginate')->with('success', 'Role updated successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to update role.');
        }
    }

    #[Middleware('can:utility.role.delete')]
    public function delete($id)
    {
        if (in_array((int) $id, $this->defaultIds)) {
            return back()->with('error', 'Default roles cannot be deleted.');
        }

        try {
            $role = Role::findOrFail($id);
            $role->delete();

            return redirect()->route('utility.roles.paginate')->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Failed to delete role.');
        }
    }
}
