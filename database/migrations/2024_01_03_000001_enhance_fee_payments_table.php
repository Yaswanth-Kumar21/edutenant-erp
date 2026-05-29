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
            // Add card payment mode if not already in enum
            // Add remarks column if missing
            if (!Schema::hasColumn('fee_payments', 'remarks')) {
                $table->text('remarks')->nullable()->after('status');
            }
            // Add year column (academic year number, e.g. 1, 2, 3)
            if (!Schema::hasColumn('fee_payments', 'year')) {
                $table->tinyInteger('year')->nullable()->after('semester');
            }
            // Add month column for monthly fees (transport)
            if (!Schema::hasColumn('fee_payments', 'month')) {
                $table->tinyInteger('month')->nullable()->after('year');
            }
        });

        // Update payment_mode enum to include card and bank_transfer
        // MySQL doesn't support ALTER COLUMN for enums cleanly, so we use MODIFY
        try {
            \Illuminate\Support\Facades\DB::statement(
                "ALTER TABLE fee_payments MODIFY COLUMN payment_mode 
                 ENUM('cash','online','cheque','dd','upi','card','bank_transfer') 
                 NOT NULL DEFAULT 'cash'"
            );
        } catch (\Exception $e) {
            // Silently skip if already updated or SQLite
        }
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
