<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use App\Models\Utility\Permission;
use App\Models\Utility\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Cache::flush();
    
        $admin = Role::query()->firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Administrator', 'description' => 'Full access to all features']
        );

        Role::query()->firstOrCreate(
            ['slug' => 'user'],
            ['name' => 'Regular User', 'description' => 'Standard access']
        );
        
        $permissions = Permission::all();

        if ($admin) {
            $admin->permissions()->sync($permissions->pluck('id')->toArray());
        }
    }
}
