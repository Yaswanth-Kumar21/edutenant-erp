<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentRegister extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * All 20 payment columns — named to match the institutional register format.
     */
    public const PAYMENT_COLUMNS = [
        'staff_salary'                  => 'Staff Salary',
        'rented_vehicle_charges'        => 'Rented Vehicle Charges',
        'diesel_charges'                => 'Diesel Charges',
        'current_bill_charges'          => 'Current Bill Charges',
        'repay_borrowings'              => 'Repay Borrowings',
        'repay_interest'                => 'Repay Interest',
        'stationary'                    => 'Stationary',
        'vehicle_finance'               => 'Vehicle Finance',
        'contingent_charges'            => 'Contingent Charges',
        'vehicle_repairs_expenditures'  => 'Vehicle Repairs and Expenditures',
        'university_dd'                 => 'University DD',
        'travelling_charges'            => 'Travelling Charges',
        'building_repairs_construction' => 'Building Repairs and Construction',
        'miscellaneous_expenditures'    => 'Miscelleneous Expenditures',
        'drinking_water_charges'        => 'Drinking Water Charges',
        'new_student_registration'      => 'New Student Registration',
        'nagarjuna_university_fee'      => 'Nagarjuna University Fee',
        'internet_bill'                 => 'Internet Bill',
        'uniform_cloth'                 => 'Uniform Cloth',
        'college_campaign_charges'      => 'College Campaign Charges',
    ];

    protected $fillable = [
        'tenant_id', 'academic_year_id', 'recorded_by', 'entry_date',
        'staff_salary', 'rented_vehicle_charges', 'diesel_charges',
        'current_bill_charges', 'repay_borrowings', 'repay_interest',
        'stationary', 'vehicle_finance', 'contingent_charges',
        'vehicle_repairs_expenditures', 'university_dd', 'travelling_charges',
        'building_repairs_construction', 'miscellaneous_expenditures',
        'drinking_water_charges', 'new_student_registration',
        'nagarjuna_university_fee', 'internet_bill', 'uniform_cloth',
        'college_campaign_charges', 'total', 'remarks',
    ];

    protected $casts = [
        'entry_date'                    => 'date',
        'staff_salary'                  => 'decimal:2',
        'rented_vehicle_charges'        => 'decimal:2',
        'diesel_charges'                => 'decimal:2',
        'current_bill_charges'          => 'decimal:2',
        'repay_borrowings'              => 'decimal:2',
        'repay_interest'                => 'decimal:2',
        'stationary'                    => 'decimal:2',
        'vehicle_finance'               => 'decimal:2',
        'contingent_charges'            => 'decimal:2',
        'vehicle_repairs_expenditures'  => 'decimal:2',
        'university_dd'                 => 'decimal:2',
        'travelling_charges'            => 'decimal:2',
        'building_repairs_construction' => 'decimal:2',
        'miscellaneous_expenditures'    => 'decimal:2',
        'drinking_water_charges'        => 'decimal:2',
        'new_student_registration'      => 'decimal:2',
        'nagarjuna_university_fee'      => 'decimal:2',
        'internet_bill'                 => 'decimal:2',
        'uniform_cloth'                 => 'decimal:2',
        'college_campaign_charges'      => 'decimal:2',
        'total'                         => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Compute and store the row total from all 20 payment columns.
     */
    public function computeTotal(): float
    {
        return array_sum(array_map(
            fn($col) => (float) ($this->{$col} ?? 0),
            array_keys(self::PAYMENT_COLUMNS)
        ));
    }
}
