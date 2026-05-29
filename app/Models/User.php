<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $fillable = [
        'tenant_id', 'role_id', 'name', 'email', 'phone',
        'avatar', 'password', 'status', 'is_super_admin',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_super_admin'    => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function staffProfile()
    {
        return $this->hasOne(Staff::class);
    }

    // ─── Role Helpers ─────────────────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin === true;
    }

    public function isCollegeAdmin(): bool
    {
        return $this->role?->name === Role::COLLEGE_ADMIN;
    }

    public function isTeacher(): bool
    {
        return $this->role?->name === Role::TEACHER;
    }

    public function isStaff(): bool
    {
        return $this->role?->name === Role::STAFF;
    }

    public function isStudent(): bool
    {
        return $this->role?->name === Role::STUDENT;
    }

    public function hasRole(string $role): bool
    {
        return $this->role?->name === $role;
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        return $this->role?->hasPermission($permission) ?? false;
    }

    public function getRoleDisplayName(): string
    {
        if ($this->isSuperAdmin()) return 'Super Admin';
        return $this->role?->display_name ?? 'Unknown';
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        // Generate initials-based avatar URL
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=4f46e5&color=fff&size=128";
    }
}
