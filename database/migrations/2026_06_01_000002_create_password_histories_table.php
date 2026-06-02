<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates the password_histories table.
     * Stores metadata about generated/reset passwords (NOT plain text).
     * Only Super Admin can view this history.
     */
    public function up(): void
    {
        Schema::create('password_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->enum('action', ['generated', 'reset', 'changed'])->default('generated');
            $table->string('generated_by')->nullable(); // 'system' or super admin name
            $table->boolean('email_sent')->default(false);
            $table->timestamp('reset_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_histories');
    }
};
