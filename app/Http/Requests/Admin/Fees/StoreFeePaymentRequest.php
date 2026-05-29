<?php

namespace App\Http\Requests\Admin\Fees;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'student_id'            => ['required', 'exists:students,id'],
            'fee_type_id'           => ['required', 'exists:fee_types,id'],
            'academic_year_id'      => ['required', 'exists:academic_years,id'],
            'amount_due'            => ['required', 'numeric', 'min:0'],
            'amount_paid'           => ['required', 'numeric', 'min:0'],
            'discount'              => ['nullable', 'numeric', 'min:0'],
            'fine'                  => ['nullable', 'numeric', 'min:0'],
            'semester'              => ['nullable', 'integer', 'min:1', 'max:12'],
            'year'                  => ['nullable', 'integer', 'min:1', 'max:6'],
            'payment_mode'          => ['required', 'in:cash,upi,card,bank_transfer,cheque,dd'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'payment_date'          => ['required', 'date'],
            'remarks'               => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required'       => 'Please select a student.',
            'fee_type_id.required'      => 'Please select a fee type.',
            'academic_year_id.required' => 'Please select an academic year.',
            'amount_due.required'       => 'Amount due is required.',
            'amount_paid.required'      => 'Amount paid is required.',
            'payment_mode.required'     => 'Please select a payment mode.',
            'payment_date.required'     => 'Payment date is required.',
        ];
    }
}
