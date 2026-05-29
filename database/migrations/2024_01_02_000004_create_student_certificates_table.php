<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Student certificate uploads.
     * Each row = one uploaded document for a student.
     * Tenant-isolated storage paths.
     */
    public function up(): void
    {
        Schema::create('student_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');

            // Certificate type (standardized codes)
            $table->string('certificate_type', 50);
            // e.g. 10th_marksheet, 12th_marksheet, tc, study_cert,
            //      caste_cert, income_cert, migration_cert, character_cert,
            //      medical_cert, photo_id, aadhaar, other

            $table->string('certificate_label', 100);   // Human-readable name
            $table->string('file_path');                 // Storage path (tenant-isolated)
            $table->string('original_filename', 255);   // Original uploaded filename
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // bytes

            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'student_id']);
            $table->index('certificate_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_certificates');
    }
};
