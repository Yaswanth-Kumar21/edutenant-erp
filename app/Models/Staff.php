<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'staff';

    protected $fillable = [
        'tenant_id', 'user_id', 'staff_role_id', 'staff_code', 'name', 'staff_type',
        'designation', 'department', 'qualification', 'role_description',
        'subject', 'email', 'phone', 'date_of_joining', 'date_of_birth',
        'gender', 'address', 'photo', 'aadhaar_number', 'pan_number',
        'bank_account', 'bank_name', 'ifsc_code',
        'monthly_salary', 'basic_salary', 'hra', 'da', 'other_allowances',
        'pf_deduction', 'tax_deduction',
        'allowed_holidays_per_month', 'salary_calculation_days', 'status',
    ];

    protected $casts = [
        'date_of_joining' => 'date',
        'date_of_birth'   => 'date',
        'monthly_salary'  => 'decimal:2',
        'basic_salary'    => 'decimal:2',
        'hra'             => 'decimal:2',
        'da'              => 'decimal:2',
        'other_allowances'=> 'decimal:2',
        'pf_deduction'    => 'decimal:2',
        'tax_deduction'   => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function tenant()       { return $this->belongsTo(Tenant::class); }
    public function user()         { return $this->belongsTo(User::class); }
    public function staffRole()    { return $this->belongsTo(StaffRole::class); }
    public function attendance()   { return $this->hasMany(StaffAttendance::class); }
    public function leaveRequests(){ return $this->hasMany(LeaveRequest::class); }
    public function payrolls()     { return $this->hasMany(Payroll::class); }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isTeaching(): bool    { return $this->staff_type === 'teaching'; }
    public function isNonTeaching(): bool { return $this->staff_type === 'non_teaching'; }

    /**
     * Calculate net salary based on attendance.
     * Month = 30 days, 2 holidays allowed per month.
     */
    public function calculateMonthlySalary(int $presentDays, int $month, int $year): float
    {
        $totalDays     = $this->salary_calculation_days ?? 30;
        $allowedLeaves = $this->allowed_holidays_per_month ?? 2;
        $effectiveDays = min($presentDays + $allowedLeaves, $totalDays);
        $perDaySalary  = $this->monthly_salary / $totalDays;
        return round($perDaySalary * $effectiveDays, 2);
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) return asset('storage/' . $this->photo);
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=059669&color=fff&size=128";
    }

    public function getGrossSalaryAttribute(): float
    {
        return (float) ($this->basic_salary + $this->hra + $this->da + $this->other_allowances)
            ?: (float) $this->monthly_salary;
    }
}
