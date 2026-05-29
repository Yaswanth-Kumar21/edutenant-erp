<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\Course;
use App\Models\FeeStructure;
use App\Models\FeeType;
use App\Models\Role;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;

/**
 * OnboardingService
 *
 * Calculates the real-time onboarding completion status for any institution.
 * Used in the Super Admin portal to show dynamic setup progress.
 *
 * 5 Steps:
 *  1. Institution Created   — Tenant record exists
 *  2. College Admin Added   — At least one college_admin user for this tenant
 *  3. Academics Configured  — Branches + Courses + Streams + Academic Years exist
 *  4. Fee Structure Set Up  — Fee Types + Fee Structures exist
 *  5. Students Added        — At least one student enrolled
 */
class OnboardingService
{
    /**
     * Get the full onboarding status for a tenant.
     *
     * @return array{
     *   steps: array,
     *   completed_count: int,
     *   total: int,
     *   percentage: int,
     *   is_complete: bool
     * }
     */
    public static function getStatus(Tenant $tenant): array
    {
        $id = $tenant->id;

        // ── Step 1: Institution Created ───────────────────────────────────
        $step1 = [
            'number'  => 1,
            'label'   => 'Institution Created',
            'detail'  => 'Institution record exists on the platform',
            'done'    => true, // Always true if we have the tenant
            'action'  => null,
            'action_label' => null,
        ];

        // ── Step 2: College Admin Added ───────────────────────────────────
        $adminRole    = Role::where('name', Role::COLLEGE_ADMIN)->first();
        $hasAdmin     = $adminRole
            ? User::where('tenant_id', $id)->where('role_id', $adminRole->id)->exists()
            : false;

        $step2 = [
            'number'  => 2,
            'label'   => 'College Admin Added',
            'detail'  => $hasAdmin
                ? 'College admin account is configured'
                : 'No college admin user exists for this institution',
            'done'    => $hasAdmin,
            'action'  => null, // Super admin creates users manually for now
            'action_label' => 'Create Admin User',
        ];

        // ── Step 3: Academics Configured ─────────────────────────────────
        $hasStreams       = Stream::where('tenant_id', $id)->exists();
        $hasCourses       = Course::where('tenant_id', $id)->exists();
        $hasBranches      = Branch::where('tenant_id', $id)->exists();
        $hasAcademicYears = AcademicYear::where('tenant_id', $id)->exists();
        $academicsOk      = $hasStreams && $hasCourses && $hasBranches && $hasAcademicYears;

        $missingAcademics = [];
        if (!$hasStreams)       $missingAcademics[] = 'Streams';
        if (!$hasCourses)       $missingAcademics[] = 'Courses';
        if (!$hasBranches)      $missingAcademics[] = 'Branches';
        if (!$hasAcademicYears) $missingAcademics[] = 'Academic Years';

        $step3 = [
            'number'  => 3,
            'label'   => 'Academics Configured',
            'detail'  => $academicsOk
                ? 'Streams, courses, branches, and academic years are set up'
                : 'Missing: ' . implode(', ', $missingAcademics),
            'done'    => $academicsOk,
            'action'  => null, // College admin configures this
            'action_label' => 'Configure Academics',
        ];

        // ── Step 4: Fee Structure Configured ─────────────────────────────
        $hasFeeTypes      = FeeType::where('tenant_id', $id)->where('is_active', true)->exists();
        $hasFeeStructures = FeeStructure::where('tenant_id', $id)->exists();
        $feesOk           = $hasFeeTypes && $hasFeeStructures;

        $missingFees = [];
        if (!$hasFeeTypes)      $missingFees[] = 'Fee Types';
        if (!$hasFeeStructures) $missingFees[] = 'Fee Structures';

        $step4 = [
            'number'  => 4,
            'label'   => 'Fee Structure Configured',
            'detail'  => $feesOk
                ? 'Fee types and structures are configured'
                : 'Missing: ' . implode(', ', $missingFees),
            'done'    => $feesOk,
            'action'  => null,
            'action_label' => 'Set Up Fees',
        ];

        // ── Step 5: Students Added ────────────────────────────────────────
        $studentCount = Student::where('tenant_id', $id)->count();
        $hasStudents  = $studentCount > 0;

        $step5 = [
            'number'  => 5,
            'label'   => 'Students Added',
            'detail'  => $hasStudents
                ? "{$studentCount} student(s) enrolled"
                : 'No students have been admitted yet',
            'done'    => $hasStudents,
            'action'  => null,
            'action_label' => 'Add First Student',
        ];

        // ── Aggregate ─────────────────────────────────────────────────────
        $steps = [$step1, $step2, $step3, $step4, $step5];
        $completedCount = collect($steps)->where('done', true)->count();
        $total          = count($steps);
        $percentage     = (int) round(($completedCount / $total) * 100);

        return [
            'steps'           => $steps,
            'completed_count' => $completedCount,
            'total'           => $total,
            'percentage'      => $percentage,
            'is_complete'     => $completedCount === $total,
            // Extra detail counts
            'student_count'   => $studentCount,
            'has_admin'       => $hasAdmin,
            'academics_ok'    => $academicsOk,
            'fees_ok'         => $feesOk,
        ];
    }

    /**
     * Get a compact onboarding badge for table rows.
     * Returns: ['percentage' => 80, 'color' => 'green', 'label' => '4/5']
     */
    public static function getBadge(Tenant $tenant): array
    {
        $status = self::getStatus($tenant);
        $pct    = $status['percentage'];

        $color = match(true) {
            $pct === 100 => 'green',
            $pct >= 60   => 'orange',
            default      => 'red',
        };

        return [
            'percentage' => $pct,
            'color'      => $color,
            'label'      => $status['completed_count'] . '/' . $status['total'],
        ];
    }
}
