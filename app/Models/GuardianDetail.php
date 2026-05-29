<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuardianDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'student_id',
        'father_name', 'father_occupation', 'father_phone', 'father_email',
        'mother_name', 'mother_occupation', 'mother_phone',
        'guardian_name', 'guardian_relation', 'guardian_phone', 'guardian_occupation',
        'annual_income', 'scholarship_eligible', 'scholarship_details',
    ];

    protected $casts = [
        'annual_income'        => 'decimal:2',
        'scholarship_eligible' => 'boolean',
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

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Get the primary contact name (father → guardian fallback).
     */
    public function getPrimaryContactNameAttribute(): string
    {
        return $this->father_name ?? $this->guardian_name ?? '—';
    }

    /**
     * Get the primary contact phone.
     */
    public function getPrimaryContactPhoneAttribute(): ?string
    {
        return $this->father_phone ?? $this->guardian_phone;
    }
}
