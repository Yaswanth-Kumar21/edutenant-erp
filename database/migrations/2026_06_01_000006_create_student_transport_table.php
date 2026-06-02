<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates the student_transport table.
     * Stores transport/vehicle assignment details per student.
     */
    public function up(): void
    {
        Schema::create('student_transport', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');

            $table->string('route', 200)->nullable();
            $table->string('boarding_point', 200)->nullable();
            $table->decimal('transport_fee', 10, 2)->default(0);

            // JSON array of semester numbers
            $table->json('applicable_semesters')->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_transport');
    }
};
