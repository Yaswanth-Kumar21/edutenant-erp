<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->enum('report_type', ['daily', 'monthly', 'semester', 'annual'])->default('monthly');
            $table->enum('entity_type', ['student', 'staff'])->default('student');
            $table->date('from_date');
            $table->date('to_date');
            $table->tinyInteger('month')->nullable();
            $table->smallInteger('year')->nullable();
            $table->tinyInteger('semester')->nullable();

            // Aggregated stats (JSON for flexibility)
            $table->json('summary_data')->nullable();

            $table->integer('total_records')->default(0);
            $table->integer('total_present')->default(0);
            $table->integer('total_absent')->default(0);
            $table->decimal('avg_percentage', 5, 2)->default(0);

            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'report_type']);
            $table->index(['tenant_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_reports');
    }
};
