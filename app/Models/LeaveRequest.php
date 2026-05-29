<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'staff_id', 'approved_by', 'leave_type',
        'from_date', 'to_date', 'total_days', 'reason',
        'status', 'rejection_reason', 'approved_at',
    ];

    protected $casts = [
        'from_date'   => 'date',
        'to_date'     => 'date',
        'approved_at' => 'datetime',
    ];

    public const LEAVE_TYPES = [
        'casual'    => 'Casual Leave',
        'sick'      => 'Sick Leave',
        'earned'    => 'Earned Leave',
        'maternity' => 'Maternity Leave',
        'paternity' => 'Paternity Leave',
        'unpaid'    => 'Unpaid Leave',
        'other'     => 'Other',
    ];

    public function tenant()     { return $this->belongsTo(Tenant::class); }
    public function staff()      { return $this->belongsTo(Staff::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }

    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }
}
