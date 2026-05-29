<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates expenses and income tables.
     * Expense categories: vehicle petrol, bills, water, electricity, travel, repairs, stationery, borrowings.
     * Income categories: borrowings, exam fee, record fee, tuition fee, vehicle fee.
     */
    public function up(): void
    {
        // Expense categories lookup
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('code', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('tenant_id');
        });

        // Expenses table
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('expense_category_id')->constrained('expense_categories')->onDelete('restrict');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->string('bill_number', 50)->nullable();
            $table->string('bill_attachment')->nullable(); // File path
            $table->enum('payment_mode', ['cash', 'online', 'cheque', 'upi'])->default('cash');
            $table->string('vendor_name', 150)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'expense_date']);
        });

        // Income categories lookup
        Schema::create('income_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('code', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('tenant_id');
        });

        // Income table (non-fee income like borrowings)
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('income_category_id')->constrained('income_categories')->onDelete('restrict');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->date('income_date');
            $table->string('reference_number', 50)->nullable();
            $table->enum('payment_mode', ['cash', 'online', 'cheque', 'upi'])->default('cash');
            $table->timestamps();
            $table->softDeletes();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'income_date']);
        });

        // Daily balance ledger (Opening Balance / Closing Balance)
        Schema::create('daily_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->date('balance_date')->unique();
            $table->decimal('opening_balance', 12, 2)->default(0);
            $table->decimal('total_income', 12, 2)->default(0);
            $table->decimal('total_expense', 12, 2)->default(0);
            $table->decimal('closing_balance', 12, 2)->default(0);
            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'balance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_balances');
        Schema::dropIfExists('incomes');
        Schema::dropIfExists('income_categories');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_categories');
    }
};
