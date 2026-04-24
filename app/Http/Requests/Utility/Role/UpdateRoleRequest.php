<?php

namespace App\Http\Requests\Utility\Role;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('utility.role.edit');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:roles,slug,' . $this->route('id')],
            'description' => ['nullable', 'string'],
            'permission_ids' => ['required', 'array'],
            'permission_ids.*' => ['exists:permissions,id'],
        ];
    }
}
