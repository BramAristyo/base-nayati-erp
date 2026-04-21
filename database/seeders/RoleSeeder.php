<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Cache::flush();
    
        $baseRoles = [
            'admin',
            'user',
        ];

        foreach ($baseRoles as $role) {
            Role::query()->firstOrCreate(['name' => $role]);
        }
        
        $permissions = Permission::all();

        Role::findByName('admin')->givePermissionTo($permissions);
    }
}
