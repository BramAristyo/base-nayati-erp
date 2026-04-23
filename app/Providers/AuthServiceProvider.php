<?php

namespace App\Providers;

use App\Models\Utility\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('admin')) return true;
        });

        $permissions = Permission::all();

        foreach ($permissions as $permission) {
            Gate::define($permission->slug, fn($user) =>
                $user->hasPermission($permission->slug)
            );
        }
    }
}
