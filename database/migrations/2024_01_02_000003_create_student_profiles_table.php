<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extended student profile data.
     * Stores additional personal/academic info not in the core students table.
     * Kept separate to keep students table lean and fast.
     */
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');

            // Extended personal info
            $table->string('aadhaar_number', 20)->nullable();
            $table->string('blood_group', 5)->nullable();   // A+, B+, O+, AB+, etc.
            $table->string('nationality', 50)->default('Indian');
            $table->string('religion', 50)->nullable();
            $table->string('caste', 100)->nullable();
            $table->string('sub_caste', 100)->nullable();
            $table->boolean('is_physically_handicapped')->default(false);
            $table->string('handicap_details', 200)->nullable();

            // Previous institution
            $table->string('previous_institution', 200)->nullable();
            $table->string('previous_institution_place', 100)->nullable();
            $table->string('previous_course', 100)->nullable();
            $table->year('previous_pass_year')->nullable();

            // University / scholarship
            $table->string('university_reg_number', 50)->nullable();
            $table->boolean('scholarship_applied')->default(false);
            $table->string('scholarship_type', 100)->nullable();  // e.g. SC/ST Scholarship, Merit
            $table->string('scholarship_amount')->nullable();

            // Emergency contact
            $table->string('emergency_contact_name', 150)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('emergency_contact_relation', 50)->nullable();

            // Hostel
            $table->boolean('hostel_required')->default(false);

            // Notes
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique('student_id');  // One profile per student
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};
