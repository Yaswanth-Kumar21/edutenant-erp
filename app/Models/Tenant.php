<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'domain', 'email', 'phone', 'address',
        'city', 'state', 'pincode', 'logo', 'website',
        'principal_name', 'affiliation_number', 'status',
        'settings', 'subscription_start', 'subscription_end',
    ];

    protected $casts = [
        'settings'           => 'array',
        'subscription_start' => 'date',
        'subscription_end'   => 'date',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function streams()
    {
        return $this->hasMany(Stream::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function academicYears()
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function feeTypes()
    {
        return $this->hasMany(FeeType::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function admissionReceipts()
    {
        return $this->hasMany(AdmissionReceipt::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function currentAcademicYear()
    {
        return $this->academicYears()->where('is_current', true)->first();
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }
}
