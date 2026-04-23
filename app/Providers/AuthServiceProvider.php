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
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) return true;
        });

        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('permissions')) {
                    $this->registerPermissions();
                }
            } catch (\Exception $e) {
                // Defensive
            }
        } else {
            $this->registerPermissions();
        }
    }

    protected function registerPermissions(): void
    {
        try {
            $permissions = Permission::all();
            foreach ($permissions as $permission) {
                Gate::define($permission->slug, fn($user) =>
                    method_exists($user, 'hasPermission') && $user->hasPermission($permission->slug)
                );
            }
        } catch (\Exception $e) {
            // Table might not exist yet
        }
    }
}
