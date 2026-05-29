<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates streams table.
     * Streams: Science, Arts etc.
     * Each stream belongs to a tenant (college).
     */
    public function up(): void
    {
        Schema::create('streams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name', 100);          // e.g. Science, Arts
            $table->string('code', 20)->nullable(); // e.g. SCI, ARTS
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('tenant_id');
            $table->unique(['tenant_id', 'name']); // Unique per tenant
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('streams');
    }
};
