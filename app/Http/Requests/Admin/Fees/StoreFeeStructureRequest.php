<?php

namespace App\Http\Requests\Admin\Fees;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeeStructureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'fee_type_id'      => ['required', 'exists:fee_types,id'],
            'branch_id'        => ['nullable', 'exists:branches,id'],
            'stream_id'        => ['nullable', 'exists:streams,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'semester'         => ['nullable', 'integer', 'min:1', 'max:12'],
            'year'             => ['nullable', 'integer', 'min:1', 'max:6'],
            'amount'           => ['required', 'numeric', 'min:0'],
            'is_active'        => ['nullable', 'boolean'],
        ];
    }
}
