<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove scholarship fields from guardian_details.
     * Scholarship data is now stored in student_scholarships table.
     * We keep annual_income as it's financial family info, not scholarship.
     */
    public function up(): void
    {
        Schema::table('guardian_details', function (Blueprint $table) {
            if (Schema::hasColumn('guardian_details', 'scholarship_eligible')) {
                $table->dropColumn('scholarship_eligible');
            }
            if (Schema::hasColumn('guardian_details', 'scholarship_details')) {
                $table->dropColumn('scholarship_details');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guardian_details', function (Blueprint $table) {
            $table->boolean('scholarship_eligible')->default(false)->after('annual_income');
            $table->text('scholarship_details')->nullable()->after('scholarship_eligible');
        });
    }
};
