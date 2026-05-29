<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $table = 'student_attendance';

    protected $fillable = [
        'tenant_id', 'student_id', 'branch_id', 'marked_by',
        'attendance_date', 'semester', 'subject', 'status', 'remarks',
    ];

    protected $casts = ['attendance_date' => 'date'];

    public function tenant()  { return $this->belongsTo(Tenant::class); }
    public function student() { return $this->belongsTo(Student::class); }
    public function branch()  { return $this->belongsTo(Branch::class); }
    public function markedBy(){ return $this->belongsTo(User::class, 'marked_by'); }
}
