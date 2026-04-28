<?php

namespace App\Http\Requests\Common;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class BasicPaginateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sortOrderRaw = $this->input('sortOrder');
        $sortOrder = $sortOrderRaw !== null ? (int) $sortOrderRaw : -1;
        $parsedOrder = $sortOrder == 1 ? 'asc' : 'desc';

        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');

        if (!$startDate && !$endDate) {
            $startDate = Carbon::now()->subDays(30)->toDateString();
            $endDate = Carbon::now()->toDateString();
        }

        $this->merge([
            'per_page'   => (int) $this->input('per_page', 25),
            'sort_by'    => $this->input('sortField', $this->input('defaultSort', 'id')),
            'sort_order' => $parsedOrder,
            'sortOrder'  => $sortOrder,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'string', 'in:asc,desc'],
            'sortField' => ['nullable', 'string'],
            'sortOrder' => ['nullable'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ];
    }
}
