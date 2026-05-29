<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id', 'sent_by', 'subject', 'body', 'channel',
        'recipient_type', 'stream_id', 'branch_id', 'student_id',
        'status', 'scheduled_at', 'sent_at', 'total_recipients', 'delivered_count',
    ];

    protected $casts = ['scheduled_at' => 'datetime', 'sent_at' => 'datetime'];

    public function tenant()  { return $this->belongsTo(Tenant::class); }
    public function sentBy()  { return $this->belongsTo(User::class, 'sent_by'); }
    public function stream()  { return $this->belongsTo(Stream::class); }
    public function branch()  { return $this->belongsTo(Branch::class); }
    public function student() { return $this->belongsTo(Student::class); }
}
