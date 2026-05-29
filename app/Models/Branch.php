<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'course_id', 'name', 'code', 'description',
        'intake_capacity', 'tuition_fee_student', 'tuition_fee_govt',
        'has_record_fee', 'is_active',
    ];

    protected $casts = [
        'has_record_fee'      => 'boolean',
        'is_active'           => 'boolean',
        'tuition_fee_student' => 'decimal:2',
        'tuition_fee_govt'    => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function stream()
    {
        return $this->course->stream ?? null;
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->course->name . ' (' . $this->name . ')';
    }
}
