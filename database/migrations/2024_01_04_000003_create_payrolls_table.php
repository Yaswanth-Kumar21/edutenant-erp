<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->tinyInteger('month');           // 1–12
            $table->smallInteger('year');           // e.g. 2026
            $table->string('payroll_number', 30)->nullable();

            // Earnings
            $table->decimal('gross_salary', 10, 2)->default(0);
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('hra', 10, 2)->default(0);
            $table->decimal('da', 10, 2)->default(0);
            $table->decimal('other_allowances', 10, 2)->default(0);

            // Attendance
            $table->integer('working_days')->default(30);
            $table->integer('present_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('leave_days')->default(0);
            $table->integer('holiday_days')->default(0);
            $table->integer('half_days')->default(0);
            $table->integer('allowed_holidays')->default(2);

            // Deductions
            $table->decimal('absent_deduction', 10, 2)->default(0);
            $table->decimal('pf_deduction', 10, 2)->default(0);
            $table->decimal('tax_deduction', 10, 2)->default(0);
            $table->decimal('other_deductions', 10, 2)->default(0);
            $table->decimal('total_deductions', 10, 2)->default(0);

            // Net
            $table->decimal('net_salary', 10, 2)->default(0);
            $table->decimal('per_day_salary', 10, 2)->default(0);

            // Payment
            $table->enum('status', ['draft', 'approved', 'paid', 'cancelled'])->default('draft');
            $table->enum('payment_mode', ['bank_transfer', 'cash', 'cheque', 'upi'])->default('bank_transfer');
            $table->date('payment_date')->nullable();
            $table->string('transaction_reference', 100)->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'staff_id']);
            $table->index(['tenant_id', 'month', 'year']);
            $table->unique(['staff_id', 'month', 'year'], 'payroll_staff_month_year_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
