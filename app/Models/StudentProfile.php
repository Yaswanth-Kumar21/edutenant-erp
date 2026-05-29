<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'student_id',
        'aadhaar_number', 'blood_group', 'nationality', 'religion',
        'caste', 'sub_caste', 'is_physically_handicapped', 'handicap_details',
        'previous_institution', 'previous_institution_place', 'previous_course', 'previous_pass_year',
        'university_reg_number', 'scholarship_applied', 'scholarship_type', 'scholarship_amount',
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation',
        'hostel_required', 'remarks',
    ];

    protected $casts = [
        'is_physically_handicapped' => 'boolean',
        'scholarship_applied'       => 'boolean',
        'hostel_required'           => 'boolean',
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
}
