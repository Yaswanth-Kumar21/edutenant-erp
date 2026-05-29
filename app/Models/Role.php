<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // System role constants
    const SUPER_ADMIN   = 'super_admin';
    const COLLEGE_ADMIN = 'college_admin';
    const STAFF         = 'staff';
    const TEACHER       = 'teacher';
    const STUDENT       = 'student';

    protected $fillable = [
        'name', 'display_name', 'description', 'permissions', 'is_system',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system'   => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }
}
