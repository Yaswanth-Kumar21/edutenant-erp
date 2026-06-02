<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates the full 7-step admission form submission.
 * Steps: Personal → Academic → Category → Guardian → Scholarship & Benefits → Documents → Receipt
 */
class StoreAdmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // ── Step 1: Personal Details ──────────────────────────────────
            'first_name'       => ['required', 'string', 'max:100'],
            'last_name'        => ['required', 'string', 'max:100'],
            'date_of_birth'    => ['nullable', 'date', 'before:today'],
            'gender'           => ['required', 'in:male,female,other'],
            'blood_group'      => ['nullable', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'],
            'aadhaar_number'   => ['nullable', 'string', 'max:20'],
            'phone'            => ['required', 'string', 'max:20'],
            'email'            => ['nullable', 'email', 'max:255'],
            'address'          => ['nullable', 'string', 'max:500'],
            'city'             => ['nullable', 'string', 'max:100'],
            'state'            => ['nullable', 'string', 'max:100'],
            'pincode'          => ['nullable', 'string', 'max:10'],
            'photo'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            // ── Step 2: Academic Details ──────────────────────────────────
            'branch_id'              => ['required', 'exists:branches,id'],
            'academic_year_id'       => ['required', 'exists:academic_years,id'],
            'marks_10th'             => ['nullable', 'numeric', 'min:0', 'max:100'],
            'marks_12th'             => ['nullable', 'numeric', 'min:0', 'max:100'],
            'previous_institution'   => ['nullable', 'string', 'max:200'],
            'current_semester'       => ['nullable', 'integer', 'min:1', 'max:12'],
            'current_year'           => ['nullable', 'integer', 'min:1', 'max:6'],

            // ── Step 3: Category & Admission ─────────────────────────────
            'category'               => ['required', 'in:GEN,OBC,SC,ST,EWS,OTHER'],
            'admission_date'         => ['required', 'date'],
            'university_reg_number'  => ['nullable', 'string', 'max:50'],
            'vehicle_opted'          => ['nullable', 'boolean'],
            'vehicle_start_date'     => ['nullable', 'date', 'required_if:vehicle_opted,1'],

            // ── Step 4: Guardian Details ──────────────────────────────────
            'father_name'            => ['nullable', 'string', 'max:150'],
            'father_occupation'      => ['nullable', 'string', 'max:100'],
            'father_phone'           => ['nullable', 'string', 'max:20'],
            'father_email'           => ['nullable', 'email'],
            'mother_name'            => ['nullable', 'string', 'max:150'],
            'mother_occupation'      => ['nullable', 'string', 'max:100'],
            'mother_phone'           => ['nullable', 'string', 'max:20'],
            'annual_income'          => ['nullable', 'numeric', 'min:0'],

            // ── Step 5: Scholarship & Benefits ───────────────────────────
            // Government Scholarship
            'govt_scholarship_eligible'  => ['nullable', 'boolean'],
            'govt_scholarship_type'      => ['nullable', 'string', 'max:100', 'required_if:govt_scholarship_eligible,1'],
            'govt_scholarship_amount'    => ['nullable', 'numeric', 'min:0'],
            'govt_scholarship_semesters' => ['nullable', 'array'],
            'govt_scholarship_semesters.*' => ['integer', 'min:1', 'max:8'],

            // College Scholarship
            'college_scholarship_eligible'  => ['nullable', 'boolean'],
            'college_scholarship_name'      => ['nullable', 'string', 'max:150', 'required_if:college_scholarship_eligible,1'],
            'college_scholarship_amount'    => ['nullable', 'numeric', 'min:0'],
            'college_scholarship_semesters' => ['nullable', 'array'],
            'college_scholarship_semesters.*' => ['integer', 'min:1', 'max:8'],

            // Transport Details (shown if vehicle_opted = 1)
            'transport_route'        => ['nullable', 'string', 'max:200'],
            'transport_boarding'     => ['nullable', 'string', 'max:200'],
            'transport_fee'          => ['nullable', 'numeric', 'min:0'],
            'transport_semesters'    => ['nullable', 'array'],
            'transport_semesters.*'  => ['integer', 'min:1', 'max:8'],
            'transport_notes'        => ['nullable', 'string', 'max:500'],

            // ── Step 6: Certificates (optional uploads) ───────────────────
            'certificates'           => ['nullable', 'array'],
            'certificates.*'         => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'certificate_types'      => ['nullable', 'array'],
            'certificate_types.*'    => ['string'],

            // ── Step 7: Admission Receipt ─────────────────────────────────
            'admission_fee'          => ['nullable', 'numeric', 'min:0'],
            'tuition_fee'            => ['nullable', 'numeric', 'min:0'],
            'other_fees'             => ['nullable', 'numeric', 'min:0'],
            'amount_paid'            => ['nullable', 'numeric', 'min:0'],
            'payment_mode'           => ['nullable', 'in:cash,online,cheque,dd,upi'],
            'transaction_reference'  => ['nullable', 'string', 'max:100'],
            'payment_date'           => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required'           => 'First name is required.',
            'last_name.required'            => 'Last name is required.',
            'gender.required'               => 'Please select a gender.',
            'phone.required'                => 'Phone number is required.',
            'branch_id.required'            => 'Please select a branch.',
            'academic_year_id.required'     => 'Please select an academic year.',
            'category.required'             => 'Please select a reservation category.',
            'admission_date.required'       => 'Admission date is required.',
            'photo.image'                   => 'Profile photo must be an image file.',
            'photo.max'                     => 'Profile photo must not exceed 2MB.',
            'certificates.*.max'            => 'Each certificate file must not exceed 5MB.',
            'govt_scholarship_type.required_if'   => 'Please select a scholarship type.',
            'college_scholarship_name.required_if' => 'Please enter the scholarship name.',
        ];
    }
}
