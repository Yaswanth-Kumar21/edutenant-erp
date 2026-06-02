<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates the email_logs table.
     * Tracks all outbound emails sent by the platform.
     */
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->string('recipient');          // Email address
            $table->string('subject');
            $table->string('mailable')->nullable(); // Mail class name
            $table->enum('status', ['sent', 'failed', 'queued'])->default('queued');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index('recipient');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
