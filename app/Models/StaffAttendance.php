<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    use HasFactory;

    protected $table = 'staff_attendance';

    protected $fillable = [
        'tenant_id', 'staff_id', 'marked_by',
        'attendance_date', 'status', 'check_in', 'check_out', 'remarks',
    ];

    protected $casts = ['attendance_date' => 'date'];

    public function tenant()  { return $this->belongsTo(Tenant::class); }
    public function staff()   { return $this->belongsTo(Staff::class); }
    public function markedBy(){ return $this->belongsTo(User::class, 'marked_by'); }
}
