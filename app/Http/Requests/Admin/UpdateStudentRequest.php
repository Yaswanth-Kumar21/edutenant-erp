<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates student profile update requests.
 */
class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'first_name'             => ['required', 'string', 'max:100'],
            'last_name'              => ['required', 'string', 'max:100'],
            'date_of_birth'          => ['nullable', 'date'],
            'gender'                 => ['nullable', 'in:male,female,other'],
            'blood_group'            => ['nullable', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'],
            'aadhaar_number'         => ['nullable', 'string', 'max:20'],
            'phone'                  => ['nullable', 'string', 'max:20'],
            'email'                  => ['nullable', 'email'],
            'address'                => ['nullable', 'string', 'max:500'],
            'city'                   => ['nullable', 'string', 'max:100'],
            'state'                  => ['nullable', 'string', 'max:100'],
            'pincode'                => ['nullable', 'string', 'max:10'],
            'photo'                  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'category'               => ['required', 'in:GEN,OBC,SC,ST,EWS,OTHER'],
            'status'                 => ['required', 'in:active,inactive,passed_out,dropped'],
            'university_reg_number'  => ['nullable', 'string', 'max:50'],
            'scholarship_eligible'   => ['nullable', 'boolean'],
            'current_semester'       => ['nullable', 'integer', 'min:1', 'max:12'],
            'current_year'           => ['nullable', 'integer', 'min:1', 'max:6'],
            'marks_10th'             => ['nullable', 'numeric', 'min:0', 'max:100'],
            'marks_12th'             => ['nullable', 'numeric', 'min:0', 'max:100'],
            'vehicle_opted'          => ['nullable', 'boolean'],
            'vehicle_start_date'     => ['nullable', 'date'],

            // Guardian
            'father_name'            => ['nullable', 'string', 'max:150'],
            'father_occupation'      => ['nullable', 'string', 'max:100'],
            'father_phone'           => ['nullable', 'string', 'max:20'],
            'mother_name'            => ['nullable', 'string', 'max:150'],
            'mother_phone'           => ['nullable', 'string', 'max:20'],
            'annual_income'          => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
