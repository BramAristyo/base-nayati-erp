<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BasicPaginateRequest;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
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

    public function paginate(BasicPaginateRequest $request)
    {
        try {
            $roles = Role::query()
                ->withCount('permissions')
                ->when($request->search, function ($query) use ($request) {
                    $query->where('name', 'like', "%{$request->search}%");
                })
                ->orderBy($request->sort_by, $request->sort_order)
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

    public function create()
    {
        try {
            return inertia('Utility/RolePermission/Create');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Error while loading roles.');
        }
    }
}
