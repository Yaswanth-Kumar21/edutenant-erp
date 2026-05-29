<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'stream_id', 'name', 'code', 'description',
        'duration_years', 'total_semesters', 'has_record_fee', 'is_active',
    ];

    protected $casts = [
        'has_record_fee' => 'boolean',
        'is_active'      => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
