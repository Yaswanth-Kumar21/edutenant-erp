<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentTransport extends Model
{
    use HasFactory;

    protected $table = 'student_transport';

    protected $fillable = [
        'tenant_id', 'student_id', 'route', 'boarding_point',
        'transport_fee', 'applicable_semesters', 'status', 'notes',
    ];

    protected $casts = [
        'applicable_semesters' => 'array',
        'transport_fee'        => 'decimal:2',
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

    public function getSemestersLabelAttribute(): string
    {
        $sems = $this->applicable_semesters ?? [];
        if (empty($sems)) return '—';
        if (count($sems) === 8) return 'All Semesters';
        return 'Sem ' . implode(', ', $sems);
    }
}
