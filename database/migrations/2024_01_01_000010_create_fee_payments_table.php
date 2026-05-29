<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates fee_payments table.
     * Records each fee payment transaction per student.
     * Supports exemptions at college discretion.
     */
    public function up(): void
    {
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('fee_type_id')->constrained('fee_types')->onDelete('restrict');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('restrict');
            $table->foreignId('collected_by')->nullable()->constrained('users')->onDelete('set null');

            $table->string('receipt_number', 50)->unique(); // Auto-generated receipt
            $table->decimal('amount_due', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('fine', 10, 2)->default(0);

            $table->integer('semester')->nullable();
            $table->integer('year')->nullable();

            $table->enum('payment_mode', ['cash', 'online', 'cheque', 'dd', 'upi'])->default('cash');
            $table->string('transaction_reference', 100)->nullable();
            $table->date('payment_date');

            $table->boolean('is_exempted')->default(false);
            $table->text('exemption_reason')->nullable();
            $table->foreignId('exempted_by')->nullable()->constrained('users')->onDelete('set null');

            $table->enum('status', ['paid', 'partial', 'pending', 'exempted'])->default('pending');
            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'student_id']);
            $table->index('receipt_number');
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
    }
};
