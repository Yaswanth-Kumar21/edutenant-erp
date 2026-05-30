<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add extended admission fields to students table.
     * These fields are needed for the full admission workflow.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Only add columns that don't already exist
            if (!Schema::hasColumn('students', 'blood_group')) {
                $table->string('blood_group', 5)->nullable();
            }
            if (!Schema::hasColumn('students', 'aadhaar_number')) {
                $table->string('aadhaar_number', 20)->nullable();
            }
            if (!Schema::hasColumn('students', 'previous_institution')) {
                $table->string('previous_institution', 200)->nullable();
            }
            if (!Schema::hasColumn('students', 'scholarship_eligible')) {
                $table->boolean('scholarship_eligible')->default(false);
            }
            if (!Schema::hasColumn('students', 'admission_step')) {
                // Track which step of the wizard was last completed (1-4)
                $table->tinyInteger('admission_step')->default(4);
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'blood_group', 'aadhaar_number', 'previous_institution',
                'scholarship_eligible', 'admission_step',
            ]);
        });
    }
};
