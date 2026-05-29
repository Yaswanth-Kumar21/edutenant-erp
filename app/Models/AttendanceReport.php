<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'branch_id', 'generated_by', 'report_type', 'entity_type',
        'from_date', 'to_date', 'month', 'year', 'semester',
        'summary_data', 'total_records', 'total_present', 'total_absent', 'avg_percentage',
    ];

    protected $casts = [
        'from_date'    => 'date',
        'to_date'      => 'date',
        'summary_data' => 'array',
    ];

    public function tenant()      { return $this->belongsTo(Tenant::class); }
    public function branch()      { return $this->belongsTo(Branch::class); }
    public function generatedBy() { return $this->belongsTo(User::class, 'generated_by'); }
}
