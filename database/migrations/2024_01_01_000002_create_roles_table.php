<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates the roles table.
     * Roles: super_admin, college_admin, staff, teacher, student
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();       // e.g. super_admin, college_admin
            $table->string('display_name', 100);        // Human-readable name
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();    // JSON array of permission keys
            $table->boolean('is_system')->default(false); // System roles cannot be deleted
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
