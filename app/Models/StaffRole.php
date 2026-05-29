<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'name', 'department', 'staff_type', 'description', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function staff()  { return $this->hasMany(Staff::class); }
}
