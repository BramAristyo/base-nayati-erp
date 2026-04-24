<?php

namespace App\Services\Utility;

use App\Enums\LogAction;
use App\Enums\LogDetailRoute;
use App\Enums\LogModule;
use App\Models\Utility\Role;
use App\Models\Utility\User;
use App\Traits\Trailable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    use Trailable;

    public function paginate(array $params): LengthAwarePaginator
    {
        return User::query()
            ->search($params['search'] ?? null)
            ->orderBy($params['sort_by'] ?? 'name', $params['sort_order'] ?? 'asc')
            ->paginate($params['per_page'] ?? 25)
            ->withQueryString();
    }

    public function store(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'approver_name' => $data['approver_name'] ?? null,
            'approver_title' => $data['approver_title'] ?? null,
            'branch_code' => $data['branch_code'],
            'position' => $data['position'],
            'is_active' => $data['is_active'],
            'password' => Hash::make('password'),
            'is_password_changed' => false,
        ]);

        if (isset($data['roles'])) {
            $user->roles()->sync(Role::whereIn('slug', $data['roles'])->pluck('id'));
        }

        if (isset($data['warehouses'])) {
            $user->warehouses()->sync($data['warehouses']);
        }

        $this->trail(LogModule::UTILITY, LogAction::CREATE, "User created: {$user->name}", $user->id, LogDetailRoute::USER);

        return $user;
    }

    public function update(int $id, array $data): User
    {
        $user = User::findOrFail($id);
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'approver_name' => $data['approver_name'] ?? null,
            'approver_title' => $data['approver_title'] ?? null,
            'branch_code' => $data['branch_code'],
            'position' => $data['position'],
            'is_active' => $data['is_active'],
        ]);

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        if (isset($data['roles'])) {
            $user->roles()->sync(Role::whereIn('slug', $data['roles'])->pluck('id'));
        }

        if (isset($data['warehouses'])) {
            $user->warehouses()->sync($data['warehouses']);
        }

        $this->trail(LogModule::UTILITY, LogAction::UPDATE, "User updated: {$user->name}", $user->id, LogDetailRoute::USER);

        return $user;
    }

    public function updatePassword(User $user, string $password): void
    {
        $user->update([
            'password' => Hash::make($password),
            'is_password_changed' => true
        ]);
    }
}
