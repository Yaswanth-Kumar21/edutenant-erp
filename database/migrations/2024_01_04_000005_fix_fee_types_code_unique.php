<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_types', function (Blueprint $table) {
            // Drop global unique on code
            $table->dropUnique(['code']);
            // Add unique per tenant (same code can exist for different tenants)
            $table->unique(['tenant_id', 'code'], 'fee_types_tenant_code_unique');
        });
    }

    public function down(): void
    {
        Schema::table('fee_types', function (Blueprint $table) {
            $table->dropUnique('fee_types_tenant_code_unique');
            $table->unique('code');
        });
    }
};
