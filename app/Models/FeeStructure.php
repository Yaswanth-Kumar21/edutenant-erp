<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'fee_type_id', 'branch_id', 'stream_id',
        'academic_year_id', 'semester', 'year', 'amount', 'is_active',
    ];

    protected $casts = [
        'amount'    => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function tenant()     { return $this->belongsTo(Tenant::class); }
    public function feeType()    { return $this->belongsTo(FeeType::class); }
    public function branch()     { return $this->belongsTo(Branch::class); }
    public function stream()     { return $this->belongsTo(Stream::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
}
