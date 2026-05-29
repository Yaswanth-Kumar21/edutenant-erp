<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stream extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['tenant_id', 'name', 'code', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function branches()
    {
        return $this->hasManyThrough(Branch::class, Course::class);
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, Branch::class);
    }
}
