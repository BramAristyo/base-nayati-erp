<?php

namespace App\Http\Requests\Common;

use Illuminate\Foundation\Http\FormRequest;

class BasicPaginateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sortOrderRaw = $this->input('sortOrder');
        $parsedOrder = $sortOrderRaw == 1 ? 'asc' : 'desc';

        $this->merge([
            'per_page'   => (int) $this->input('per_page', 25),
            'sort_by'    => $this->input('sortField', 'created_at'),
            'sort_order' => $parsedOrder,
        ]);
    }

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'search' => ['nullable', 'string'],
            'sortField' => ['nullable', 'string'],
            'sortOrder' => ['nullable', 'integer'],
        ];
    }
}
