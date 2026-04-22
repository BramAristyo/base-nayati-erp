<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
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
}
