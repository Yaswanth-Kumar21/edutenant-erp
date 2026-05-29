<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the tenants table for multi-tenant architecture.
     * Each tenant represents a college/institution.
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // College name
            $table->string('slug')->unique();                // URL-friendly identifier
            $table->string('domain')->nullable()->unique();  // Custom domain (optional)
            $table->string('email')->unique();               // Primary contact email
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('logo')->nullable();              // Logo file path
            $table->string('website')->nullable();
            $table->string('principal_name')->nullable();
            $table->string('affiliation_number')->nullable(); // University affiliation
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->json('settings')->nullable();            // Tenant-specific settings
            $table->date('subscription_start')->nullable();
            $table->date('subscription_end')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('slug');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
