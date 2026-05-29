<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates fee_types table.
     * Defines all fee categories: uniform, exam, UDF, record, vehicle, tuition, other, internship.
     * Fee amounts are configured per tenant.
     */
    public function up(): void
    {
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name', 100);             // e.g. Uniform Fee, Exam Fee
            $table->string('code', 30)->unique();     // e.g. UNIFORM, EXAM, UDF, RECORD
            $table->text('description')->nullable();

            // Fee applicability rules
            $table->enum('frequency', ['one_time', 'per_semester', 'per_year', 'monthly'])
                  ->default('one_time');
            $table->boolean('applicable_all_streams')->default(true);
            $table->boolean('applicable_all_branches')->default(true);
            $table->boolean('can_be_exempted')->default(true); // College discretion

            // Amount (base amount; branch-specific overrides in fee_structures)
            $table->decimal('amount', 10, 2)->default(0);

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('tenant_id');
        });

        /**
         * Fee structures table - defines specific amounts per branch/semester/year.
         * Allows fine-grained fee configuration.
         */
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('fee_type_id')->constrained('fee_types')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->foreignId('stream_id')->nullable()->constrained('streams')->onDelete('cascade');
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->onDelete('cascade');
            $table->integer('semester')->nullable();   // null = all semesters
            $table->integer('year')->nullable();       // null = all years
            $table->decimal('amount', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('fee_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_types');
    }
};
