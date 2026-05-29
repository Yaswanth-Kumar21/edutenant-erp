<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates students table.
     * Core student data with admission details, reservation category,
     * and university registration number for scholarship tracking.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('restrict');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Admission details
            $table->string('admission_number', 30)->unique(); // Running serial number
            $table->date('admission_date');
            $table->string('university_reg_number', 50)->nullable(); // Given by university later

            // Personal details
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('father_name', 150)->nullable();
            $table->string('mother_name', 150)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('photo')->nullable();

            // Academic details
            $table->decimal('marks_10th', 5, 2)->nullable();  // 10th marks percentage
            $table->decimal('marks_12th', 5, 2)->nullable();  // 12th marks percentage
            $table->integer('current_semester')->default(1);
            $table->integer('current_year')->default(1);

            // Reservation / Category
            $table->enum('category', ['GEN', 'OBC', 'SC', 'ST', 'EWS', 'OTHER'])->default('GEN');

            // Status
            $table->enum('status', ['active', 'inactive', 'passed_out', 'dropped'])->default('active');

            // Certificate collection tracking (JSON flags)
            $table->json('certificates_submitted')->nullable(); // Track which certs submitted

            // Vehicle opt-in
            $table->boolean('vehicle_opted')->default(false);
            $table->date('vehicle_start_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for tenant isolation and common queries
            $table->index('tenant_id');
            $table->index(['tenant_id', 'branch_id']);
            $table->index(['tenant_id', 'status']);
            $table->index('admission_number');
            $table->index('university_reg_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
