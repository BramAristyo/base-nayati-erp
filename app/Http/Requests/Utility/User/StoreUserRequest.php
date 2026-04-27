<?php

namespace App\Http\Requests\Utility\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('utility.user.create');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'approver_name' => ['nullable', 'string', 'max:255'],
            'approver_title' => ['nullable', 'string', 'max:255'],
            'branch_code' => ['required', 'string', 'max:10'],
            'position' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'roles' => ['required', 'array'],
            'roles.*' => ['string', 'exists:roles,slug'],
            'warehouses' => ['nullable', 'array'],
            'warehouses.*' => ['integer', 'exists:warehouses,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*.permission_id' => ['required', 'integer', 'exists:permissions,id'],
            'permissions.*.is_denied' => ['required', 'boolean'],
        ];
    }
}
