<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'branch_id', 'academic_year_id', 'user_id',
        'admission_number', 'admission_date', 'university_reg_number',
        'first_name', 'last_name', 'father_name', 'mother_name',
        'date_of_birth', 'gender', 'blood_group', 'aadhaar_number',
        'phone', 'email', 'address', 'city', 'state', 'pincode', 'photo',
        'marks_10th', 'marks_12th', 'previous_institution', 'current_semester', 'current_year',
        'category', 'status', 'scholarship_eligible', 'certificates_submitted',
        'vehicle_opted', 'vehicle_start_date', 'admission_step',
    ];

    protected $casts = [
        'admission_date'          => 'date',
        'date_of_birth'           => 'date',
        'vehicle_start_date'      => 'date',
        'certificates_submitted'  => 'array',
        'vehicle_opted'           => 'boolean',
        'scholarship_eligible'    => 'boolean',
        'marks_10th'              => 'decimal:2',
        'marks_12th'              => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feePayments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function attendance()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function profile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function guardian()
    {
        return $this->hasOne(GuardianDetail::class);
    }

    public function certificates()
    {
        return $this->hasMany(StudentCertificate::class);
    }

    public function admissionReceipts()
    {
        return $this->hasMany(AdmissionReceipt::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        $name = urlencode($this->full_name);
        return "https://ui-avatars.com/api/?name={$name}&background=4f46e5&color=fff&size=128";
    }

    public function hasCertificate(string $cert): bool
    {
        return in_array($cert, $this->certificates_submitted ?? []);
    }

    public function getTotalFeesPaidAttribute(): float
    {
        return $this->feePayments()->where('status', 'paid')->sum('amount_paid');
    }

    public function getAttendancePercentageAttribute(): float
    {
        $total   = $this->attendance()->count();
        $present = $this->attendance()->where('status', 'present')->count();
        return $total > 0 ? round(($present / $total) * 100, 2) : 0;
    }
}
