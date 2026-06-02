<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds first-login and password-change-required fields to users.
     * Used for the College Admin first-login forced password change flow.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('must_change_password')->default(false)->after('status');
            $table->timestamp('password_changed_at')->nullable()->after('must_change_password');
            $table->boolean('is_impersonating')->default(false)->after('password_changed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['must_change_password', 'password_changed_at', 'is_impersonating']);
        });
    }
};
