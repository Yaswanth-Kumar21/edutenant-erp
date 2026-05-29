<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates staff table.
     * Covers both teaching and non-teaching staff.
     * Salary calculated on 30-day month basis with 2 holidays/month allowed.
     */
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            $table->string('staff_code', 30)->nullable();
            $table->string('name', 150);
            $table->enum('staff_type', ['teaching', 'non_teaching']);
            $table->string('designation', 100)->nullable(); // e.g. Professor, Lecturer, Clerk
            $table->string('role_description', 150)->nullable(); // For non-teaching: Peon, Driver etc.
            $table->string('subject', 100)->nullable();    // For teaching staff
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->date('date_of_joining')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('address')->nullable();
            $table->string('photo')->nullable();

            // Salary details
            $table->decimal('monthly_salary', 10, 2)->default(0);
            $table->integer('allowed_holidays_per_month')->default(2); // As per requirement
            $table->integer('salary_calculation_days')->default(30);   // 30-day month

            $table->enum('status', ['active', 'inactive', 'resigned'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'staff_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
