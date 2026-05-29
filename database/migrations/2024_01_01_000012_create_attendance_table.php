<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates attendance tables for both students and staff.
     * Student attendance: per class, per working day.
     * Staff attendance: daily with holiday tracking.
     */
    public function up(): void
    {
        // Student attendance
        Schema::create('student_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('marked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('attendance_date');
            $table->integer('semester');
            $table->string('subject', 100)->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'holiday'])->default('absent');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'student_id']);
            $table->index(['tenant_id', 'attendance_date']);
            $table->unique(['student_id', 'attendance_date', 'subject']); // One record per student per subject per day
        });

        // Staff attendance
        Schema::create('staff_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('marked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'holiday', 'half_day', 'leave'])->default('present');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'staff_id']);
            $table->unique(['staff_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_attendance');
        Schema::dropIfExists('student_attendance');
    }
};
