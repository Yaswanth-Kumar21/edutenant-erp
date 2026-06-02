<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentScholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'student_id', 'scholarship_category',
        'scholarship_type', 'scholarship_name', 'amount',
        'applicable_semesters', 'start_date', 'end_date',
        'status', 'remarks',
    ];

    protected $casts = [
        'applicable_semesters' => 'array',
        'amount'               => 'decimal:2',
        'start_date'           => 'date',
        'end_date'             => 'date',
    ];

    // ── Government scholarship type constants ─────────────────────────────────
    public const GOVT_TYPES = [
        'sc_scholarship'       => 'SC Scholarship',
        'st_scholarship'       => 'ST Scholarship',
        'bc_scholarship'       => 'BC Scholarship',
        'ews_scholarship'      => 'EWS Scholarship',
        'minority_scholarship' => 'Minority Scholarship',
        'merit_scholarship'    => 'Merit Scholarship',
        'other'                => 'Other',
    ];

    // ── College scholarship name examples ─────────────────────────────────────
    public const COLLEGE_NAMES = [
        'Merit Scholarship',
        'Sports Scholarship',
        'Alumni Scholarship',
        'Management Scholarship',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isGovernment(): bool
    {
        return $this->scholarship_category === 'government';
    }

    public function isCollege(): bool
    {
        return $this->scholarship_category === 'college';
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->isGovernment()) {
            return self::GOVT_TYPES[$this->scholarship_type] ?? $this->scholarship_type ?? 'Government Scholarship';
        }
        return $this->scholarship_name ?? 'College Scholarship';
    }

    public function getSemestersLabelAttribute(): string
    {
        $sems = $this->applicable_semesters ?? [];
        if (empty($sems)) return '—';
        if (count($sems) === 8) return 'All Semesters';
        return 'Sem ' . implode(', ', $sems);
    }
}
