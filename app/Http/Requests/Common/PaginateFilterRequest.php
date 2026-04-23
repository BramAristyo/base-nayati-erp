<?php

namespace App\Http\Requests\Common;

use Illuminate\Foundation\Http\FormRequest;

class PaginateFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sortOrderRaw = $this->input('sortOrder');
        $parsedOrder = $sortOrderRaw == 1 ? 'asc' : 'desc';

        $dataToMerge = [
            'per_page'   => (int) $this->input('per_page', 25),
            'sort_by'    => $this->input('sortField', 'created_at'),
            'sort_order' => $parsedOrder,
        ];

        if (!$this->filled('start_date') && !$this->filled('end_date')) {
            $dataToMerge['start_date'] = now()->subDays(30)->toDateString();
            $dataToMerge['end_date'] = now()->toDateString();
        }

        $this->merge($dataToMerge);
    }

    public function rules(): array
    {
        return [];
    }
}
