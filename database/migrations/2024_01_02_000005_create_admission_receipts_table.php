<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Admission receipts table.
     * Generated at the time of student admission.
     * Separate from fee_payments (which are recurring fee collections).
     * This is the one-time admission acknowledgement receipt.
     */
    public function up(): void
    {
        Schema::create('admission_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('restrict');
            $table->foreignId('generated_by')->constrained('users')->onDelete('restrict');

            // Receipt identification
            $table->string('receipt_number', 30)->unique();
            // Format: ADM-RCPT-YYYY-NNNNN (e.g. ADM-RCPT-2026-00001)

            // Fee breakdown at time of admission
            $table->decimal('admission_fee', 10, 2)->default(0);
            $table->decimal('tuition_fee', 10, 2)->default(0);
            $table->decimal('other_fees', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('balance_due', 10, 2)->default(0);

            // Payment details
            $table->enum('payment_mode', ['cash', 'online', 'cheque', 'dd', 'upi'])->default('cash');
            $table->string('transaction_reference', 100)->nullable();
            $table->date('payment_date');

            // Status
            $table->enum('status', ['paid', 'partial', 'pending'])->default('paid');

            // JSON snapshot of fee breakdown for audit trail
            $table->json('fee_details')->nullable();

            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'student_id']);
            $table->index('receipt_number');
            $table->index(['tenant_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_receipts');
    }
};
