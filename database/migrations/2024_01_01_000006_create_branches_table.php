<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates branches table.
     * Branches are specializations within a course.
     * e.g. B.Sc (MPC), B.Sc (MPCs), B.Sc (BiPC)
     */
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->string('name', 150);            // e.g. B.Sc (MPC)
            $table->string('code', 30)->nullable();  // e.g. MPC, MPCS, BIPC
            $table->text('description')->nullable();
            $table->integer('intake_capacity')->default(60); // Max students per year
            $table->decimal('tuition_fee_student', 10, 2)->default(0); // Student portion of tuition
            $table->decimal('tuition_fee_govt', 10, 2)->default(0);    // Govt portion of tuition
            $table->boolean('has_record_fee')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('tenant_id');
            $table->index('course_id');
            $table->unique(['tenant_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
