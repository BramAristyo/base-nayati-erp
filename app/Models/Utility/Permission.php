<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name', 'slug', 'module', 'sub_module', 'action'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }
}
