<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Enhance fee_payments table with additional columns needed
     * for the advanced fee management module.
     */
    public function up(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            // Add remarks column if missing
            if (!Schema::hasColumn('fee_payments', 'remarks')) {
                $table->text('remarks')->nullable();
            }
            // Add year column (academic year number, e.g. 1, 2, 3)
            if (!Schema::hasColumn('fee_payments', 'year')) {
                $table->tinyInteger('year')->nullable();
            }
            // Add month column for monthly fees (transport)
            if (!Schema::hasColumn('fee_payments', 'month')) {
                $table->tinyInteger('month')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            $cols = ['remarks', 'year', 'month'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('fee_payments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
