<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates payment_registers table.
     * Tracks the college payments register with 20 fixed expense categories
     * as defined by the institution's internal ledger format.
     */
    public function up(): void
    {
        Schema::create('payment_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('restrict');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('entry_date');

            // ── 20 Fixed Payment Columns ──────────────────────────────────
            $table->decimal('staff_salary', 12, 2)->default(0);
            $table->decimal('rented_vehicle_charges', 12, 2)->default(0);
            $table->decimal('diesel_charges', 12, 2)->default(0);
            $table->decimal('current_bill_charges', 12, 2)->default(0);
            $table->decimal('repay_borrowings', 12, 2)->default(0);
            $table->decimal('repay_interest', 12, 2)->default(0);
            $table->decimal('stationary', 12, 2)->default(0);
            $table->decimal('vehicle_finance', 12, 2)->default(0);
            $table->decimal('contingent_charges', 12, 2)->default(0);
            $table->decimal('vehicle_repairs_expenditures', 12, 2)->default(0);
            $table->decimal('university_dd', 12, 2)->default(0);
            $table->decimal('travelling_charges', 12, 2)->default(0);
            $table->decimal('building_repairs_construction', 12, 2)->default(0);
            $table->decimal('miscellaneous_expenditures', 12, 2)->default(0);
            $table->decimal('drinking_water_charges', 12, 2)->default(0);
            $table->decimal('new_student_registration', 12, 2)->default(0);
            $table->decimal('nagarjuna_university_fee', 12, 2)->default(0);
            $table->decimal('internet_bill', 12, 2)->default(0);
            $table->decimal('uniform_cloth', 12, 2)->default(0);
            $table->decimal('college_campaign_charges', 12, 2)->default(0);

            $table->decimal('total', 12, 2)->default(0); // stored computed total
            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'academic_year_id']);
            $table->index(['tenant_id', 'entry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_registers');
    }
};
