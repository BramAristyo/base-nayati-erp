<?php

namespace App\Traits;
use Illuminate\Support\Collection;

trait HasPermission
{
    protected ?Collection $cachedPermissions = null;
    protected ?Collection $cachedDenied = null;

    public function hasPermission(string $slug): bool
    {
        if ($this->isDenied($slug)) {
            return false;
        }

        if ($this->hasDirectPermission($slug)) {
            return true;
        }

        return $this->hasRolePermission($slug);
    }

    public function hasAnyPermission(array $slugs): bool
    {
        foreach ($slugs as $slug) {
            if ($this->hasPermission($slug)) return true;
        }
        return false;
    }

    public function hasAllPermissions(array $slugs): bool
    {
        foreach ($slugs as $slug) {
            if (!$this->hasPermission($slug)) return false;
        }
        return true;
    }

    public function getAllPermissions(): Collection
    {
        $deniedSlugs = $this->getDeniedPermissions()->pluck('slug');

        $rolePermissions = $this->roles->flatMap(fn($role) => $role->permissions);
        $directPermissions = $this->getDirectPermissions();

        return $rolePermissions->concat($directPermissions)
            ->reject(fn($p) => $deniedSlugs->contains($p->slug))
            ->unique('slug')
            ->values();
    }

    private function isDenied(string $slug): bool
    {
        return $this->getDeniedPermissions()->contains('slug', $slug);
    }

    private function hasDirectPermission(string $slug): bool
    {
        return $this->getDirectPermissions()->contains('slug', $slug);
    }

    private function hasRolePermission(string $slug): bool
    {
        return $this->roles
            ->flatMap(fn($role) => $role->permissions)
            ->contains('slug', $slug);
    }

    private function getDirectPermissions(): Collection
    {
        if (!$this->cachedPermissions) {
            $this->cachedPermissions = $this->permissions
                ->filter(fn($p) => $p->pivot->is_denied == false);
        }
        return $this->cachedPermissions;
    }

    private function getDeniedPermissions(): Collection
    {
        if (!$this->cachedDenied) {
            $this->cachedDenied = $this->permissions
                ->filter(fn($p) => $p->pivot->is_denied == true);
        }
        return $this->cachedDenied;
    }

    public function clearPermissionCache(): void
    {
        $this->cachedPermissions = null;
        $this->cachedDenied = null;
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles->contains('slug', $slug);
    }
}
