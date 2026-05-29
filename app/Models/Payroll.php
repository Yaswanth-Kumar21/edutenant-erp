<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'staff_id', 'generated_by', 'month', 'year', 'payroll_number',
        'gross_salary', 'basic_salary', 'hra', 'da', 'other_allowances',
        'working_days', 'present_days', 'absent_days', 'leave_days',
        'holiday_days', 'half_days', 'allowed_holidays',
        'absent_deduction', 'pf_deduction', 'tax_deduction',
        'other_deductions', 'total_deductions', 'net_salary', 'per_day_salary',
        'status', 'payment_mode', 'payment_date', 'transaction_reference', 'remarks',
    ];

    protected $casts = [
        'payment_date'     => 'date',
        'gross_salary'     => 'decimal:2',
        'basic_salary'     => 'decimal:2',
        'hra'              => 'decimal:2',
        'da'               => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'absent_deduction' => 'decimal:2',
        'pf_deduction'     => 'decimal:2',
        'tax_deduction'    => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary'       => 'decimal:2',
        'per_day_salary'   => 'decimal:2',
    ];

    public const MONTH_NAMES = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
    ];

    public function tenant()      { return $this->belongsTo(Tenant::class); }
    public function staff()       { return $this->belongsTo(Staff::class); }
    public function generatedBy() { return $this->belongsTo(User::class, 'generated_by'); }

    public function getMonthNameAttribute(): string
    {
        return self::MONTH_NAMES[$this->month] ?? '';
    }

    public function getPayPeriodAttribute(): string
    {
        return ($this->month_name ?? '') . ' ' . $this->year;
    }

    public function isDraft(): bool    { return $this->status === 'draft'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isPaid(): bool     { return $this->status === 'paid'; }
}
