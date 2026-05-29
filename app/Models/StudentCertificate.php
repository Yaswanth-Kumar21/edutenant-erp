<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class StudentCertificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'student_id',
        'certificate_type', 'certificate_label',
        'file_path', 'original_filename', 'mime_type', 'file_size',
        'is_verified', 'verified_by', 'verified_at', 'remarks',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'file_size'   => 'integer',
    ];

    /**
     * All supported certificate types with their labels.
     */
    public const TYPES = [
        '10th_marksheet' => '10th Marksheet',
        '12th_marksheet' => '12th Marksheet',
        'tc'             => 'Transfer Certificate (TC)',
        'study_cert'     => 'Study Certificate',
        'caste_cert'     => 'Caste Certificate',
        'income_cert'    => 'Income Certificate',
        'migration_cert' => 'Migration Certificate',
        'character_cert' => 'Character Certificate',
        'medical_cert'   => 'Medical Certificate',
        'photo_id'       => 'Photo ID Proof',
        'aadhaar'        => 'Aadhaar Card',
        'other'          => 'Other Document',
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

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    /**
     * Get the public URL for this certificate file.
     */
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get human-readable file size.
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }

    /**
     * Check if this is an image file.
     */
    public function getIsImageAttribute(): bool
    {
        return str_starts_with($this->mime_type ?? '', 'image/');
    }

    /**
     * Check if this is a PDF file.
     */
    public function getIsPdfAttribute(): bool
    {
        return $this->mime_type === 'application/pdf';
    }
}
