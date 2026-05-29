<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdmissionReceipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'student_id', 'academic_year_id', 'generated_by',
        'receipt_number',
        'admission_fee', 'tuition_fee', 'other_fees', 'total_amount',
        'amount_paid', 'balance_due',
        'payment_mode', 'transaction_reference', 'payment_date',
        'status', 'fee_details', 'remarks',
    ];

    protected $casts = [
        'admission_fee'  => 'decimal:2',
        'tuition_fee'    => 'decimal:2',
        'other_fees'     => 'decimal:2',
        'total_amount'   => 'decimal:2',
        'amount_paid'    => 'decimal:2',
        'balance_due'    => 'decimal:2',
        'payment_date'   => 'date',
        'fee_details'    => 'array',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'paid'    => '#dcfce7',
            'partial' => '#fef3c7',
            'pending' => '#fee2e2',
            default   => '#f3f4f6',
        };
    }

    public function getStatusTextColorAttribute(): string
    {
        return match($this->status) {
            'paid'    => '#166534',
            'partial' => '#92400e',
            'pending' => '#991b1b',
            default   => '#374151',
        };
    }
}
