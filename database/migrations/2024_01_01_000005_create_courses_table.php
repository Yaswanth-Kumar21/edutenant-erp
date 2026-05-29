<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates courses table.
     * Courses belong to a stream. e.g. B.Sc, BA, BCom
     * Duration in years (typically 3 years for UG).
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('stream_id')->constrained('streams')->onDelete('cascade');
            $table->string('name', 100);           // e.g. B.Sc, BA, BCom
            $table->string('code', 20)->nullable(); // e.g. BSC, BA, BCOM
            $table->text('description')->nullable();
            $table->integer('duration_years')->default(3);    // Course duration
            $table->integer('total_semesters')->default(6);   // Total semesters
            $table->boolean('has_record_fee')->default(false); // Record fee applicable
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('tenant_id');
            $table->index('stream_id');
            $table->unique(['tenant_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
