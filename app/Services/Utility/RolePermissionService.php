<?php

namespace App\Services\Utility;

use App\Enums\LogAction;
use App\Enums\LogModule;
use App\Models\Utility\Permission;
use App\Models\Utility\Role;
use App\Traits\Trailable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RolePermissionService
{
    use Trailable;

    public function paginate(array $params): LengthAwarePaginator
    {
        return Role::query()
            ->withCount('permissions')
            ->when($params['search'] ?? null, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            })
            ->orderBy($params['sort_by'] ?? 'created_at', $params['sort_order'] ?? 'desc')
            ->paginate($params['per_page'] ?? 25);
    }

    public function getAll(): Collection
    {
        return Role::with('permissions')->get();
    }

    public function permissions(): Collection
    {
        return Permission::all();
    }

    public function find(int $id): Role
    {
        return Role::with('permissions')->findOrFail($id);
    }

    public function getGroupedPermissions(): \Illuminate\Support\Collection
    {
        return Permission::all()->groupBy('module')->map(function ($module) {
            return $module->groupBy('sub_module');
        });
    }

    public function store(array $data): Role
    {
        $role = Role::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
        ]);

        if (!empty($data['permission_ids'])) {
            $role->permissions()->sync($data['permission_ids']);
        }

        $this->trail(LogModule::UTILITY, LogAction::CREATE, "Role created: {$role->name}", $role->id);

        return $role;
    }

    public function update(int $id, array $data): Role
    {
        $role = Role::findOrFail($id);
        $role->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
        ]);

        if (isset($data['permission_ids'])) {
            $role->permissions()->sync($data['permission_ids']);
        }

        $this->trail(LogModule::UTILITY, LogAction::UPDATE, "Role updated: {$role->name}", $role->id);

        return $role;
    }

    public function delete(int $id): void
    {
        $role = Role::findOrFail($id);
        $roleName = $role->name;
        $role->delete();

        $this->trail(LogModule::UTILITY, LogAction::DELETE, "Role deleted: {$roleName}", $id);
    }
}
