<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Guardian / parent details for each student.
     * Separated from students table for normalization.
     */
    public function up(): void
    {
        Schema::create('guardian_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');

            // Father details
            $table->string('father_name', 150)->nullable();
            $table->string('father_occupation', 100)->nullable();
            $table->string('father_phone', 20)->nullable();
            $table->string('father_email')->nullable();

            // Mother details
            $table->string('mother_name', 150)->nullable();
            $table->string('mother_occupation', 100)->nullable();
            $table->string('mother_phone', 20)->nullable();

            // Primary guardian (if different from parents)
            $table->string('guardian_name', 150)->nullable();
            $table->string('guardian_relation', 50)->nullable();
            $table->string('guardian_phone', 20)->nullable();
            $table->string('guardian_occupation', 100)->nullable();

            // Financial info
            $table->decimal('annual_income', 12, 2)->nullable();
            $table->boolean('scholarship_eligible')->default(false);
            $table->text('scholarship_details')->nullable();

            $table->timestamps();

            $table->index('tenant_id');
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardian_details');
    }
};
