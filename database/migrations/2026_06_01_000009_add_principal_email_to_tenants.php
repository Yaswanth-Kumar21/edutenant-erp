<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add principal_email to tenants table.
     *
     * This is the College Admin's personal login email — separate from
     * the institution's general contact email (tenants.email).
     *
     * tenants.email        = institution contact email (e.g. info@college.edu)
     * tenants.principal_email = principal's personal email used for admin login
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('principal_email')->nullable()->after('principal_name');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('principal_email');
        });
    }
};
