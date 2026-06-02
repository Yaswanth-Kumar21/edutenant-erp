<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates the student_scholarships table.
     * Stores both government and college scholarship records per student.
     */
    public function up(): void
    {
        Schema::create('student_scholarships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');

            // government | college
            $table->enum('scholarship_category', ['government', 'college']);

            // For government: SC Scholarship, ST Scholarship, BC Scholarship, etc.
            // For college: Merit Scholarship, Sports Scholarship, etc.
            $table->string('scholarship_type', 100)->nullable();
            $table->string('scholarship_name', 150)->nullable();

            $table->decimal('amount', 10, 2)->default(0);

            // JSON array of semester numbers: [1,2,3] or [1,2,3,4,5,6,7,8]
            $table->json('applicable_semesters')->nullable();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'student_id']);
            $table->index(['student_id', 'scholarship_category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_scholarships');
    }
};
