<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name', 100);                    // e.g. HOD, Class Teacher, Lab Assistant
            $table->string('department', 100)->nullable();  // e.g. Science, Commerce
            $table->enum('staff_type', ['teaching', 'non_teaching', 'both'])->default('both');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('tenant_id');
            $table->unique(['tenant_id', 'name']);
        });

        // Add staff_role_id FK to staff table
        Schema::table('staff', function (Blueprint $table) {
            $table->foreignId('staff_role_id')->nullable()->after('designation')
                  ->constrained('staff_roles')->onDelete('set null');
            $table->string('department', 100)->nullable()->after('staff_role_id');
            $table->string('qualification', 150)->nullable()->after('department');
            $table->string('aadhaar_number', 20)->nullable()->after('qualification');
            $table->string('pan_number', 20)->nullable()->after('aadhaar_number');
            $table->string('bank_account', 30)->nullable()->after('pan_number');
            $table->string('bank_name', 100)->nullable()->after('bank_account');
            $table->string('ifsc_code', 20)->nullable()->after('bank_name');
            $table->decimal('basic_salary', 10, 2)->default(0)->after('monthly_salary');
            $table->decimal('hra', 10, 2)->default(0)->after('basic_salary');
            $table->decimal('da', 10, 2)->default(0)->after('hra');
            $table->decimal('other_allowances', 10, 2)->default(0)->after('da');
            $table->decimal('pf_deduction', 10, 2)->default(0)->after('other_allowances');
            $table->decimal('tax_deduction', 10, 2)->default(0)->after('pf_deduction');
        });
    }

    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropForeign(['staff_role_id']);
            $table->dropColumn([
                'staff_role_id', 'department', 'qualification',
                'aadhaar_number', 'pan_number', 'bank_account', 'bank_name', 'ifsc_code',
                'basic_salary', 'hra', 'da', 'other_allowances', 'pf_deduction', 'tax_deduction',
            ]);
        });
        Schema::dropIfExists('staff_roles');
    }
};
