<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeePayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'student_id', 'fee_type_id', 'academic_year_id', 'collected_by',
        'receipt_number', 'amount_due', 'amount_paid', 'discount', 'fine',
        'semester', 'year', 'month', 'payment_mode', 'transaction_reference', 'payment_date',
        'is_exempted', 'exemption_reason', 'exempted_by', 'status', 'remarks',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_due'   => 'decimal:2',
        'amount_paid'  => 'decimal:2',
        'discount'     => 'decimal:2',
        'fine'         => 'decimal:2',
        'is_exempted'  => 'boolean',
    ];

    /**
     * All supported payment modes.
     */
    public const PAYMENT_MODES = [
        'cash'          => 'Cash',
        'upi'           => 'UPI',
        'card'          => 'Card',
        'bank_transfer' => 'Bank Transfer',
        'cheque'        => 'Cheque',
        'dd'            => 'Demand Draft',
        'online'        => 'Online',
    ];

    public function tenant()      { return $this->belongsTo(Tenant::class); }
    public function student()     { return $this->belongsTo(Student::class); }
    public function feeType()     { return $this->belongsTo(FeeType::class); }
    public function academicYear(){ return $this->belongsTo(AcademicYear::class); }
    public function collectedBy() { return $this->belongsTo(User::class, 'collected_by'); }
    public function exemptedBy()  { return $this->belongsTo(User::class, 'exempted_by'); }

    public function getBalanceAttribute(): float
    {
        return $this->amount_due - $this->amount_paid - $this->discount + $this->fine;
    }
}
