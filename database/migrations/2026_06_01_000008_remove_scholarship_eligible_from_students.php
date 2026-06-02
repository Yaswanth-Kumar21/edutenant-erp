<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove the simple scholarship_eligible boolean from students table.
     * Scholarship eligibility is now tracked in student_scholarships table
     * with full details (type, amount, semesters, etc.).
     *
     * We keep the column but rename intent — it now means "has any scholarship"
     * and is computed from student_scholarships. We keep it for backward compat
     * with existing views that reference it.
     *
     * Actually: we keep it as a computed/cached flag for performance.
     * No structural change needed — just stop using it in the admission form.
     */
    public function up(): void
    {
        // No structural change — scholarship_eligible stays as a cached flag
        // It will be auto-set to true when a scholarship record is created
    }

    public function down(): void
    {
        // Nothing to reverse
    }
};
