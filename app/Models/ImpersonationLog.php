<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImpersonationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'super_admin_id', 'tenant_id', 'impersonated_user_id',
        'ip_address', 'login_time', 'logout_time',
    ];

    protected $casts = [
        'login_time'  => 'datetime',
        'logout_time' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function superAdmin()
    {
        return $this->belongsTo(User::class, 'super_admin_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function impersonatedUser()
    {
        return $this->belongsTo(User::class, 'impersonated_user_id');
    }
}
