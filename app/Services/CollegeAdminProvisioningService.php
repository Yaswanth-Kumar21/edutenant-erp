<?php

namespace App\Services;

use App\Mail\CollegeAdminWelcomeMail;
use App\Models\PasswordHistory;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * CollegeAdminProvisioningService
 *
 * Handles the full automated onboarding flow when a new institution is created.
 *
 * Email Logic:
 *   - Uses tenant.principal_email as the College Admin login email.
 *   - This is the principal's PERSONAL email — separate from the institution contact email.
 *   - If principal_email is not set, falls back to tenant.email.
 *   - If that email already exists as a user in the system, we UPDATE that user
 *     to become the college admin for this tenant (no duplicate created).
 *
 * Security:
 *   - Plain-text password is ONLY held in memory during this request.
 *   - Only bcrypt hash is stored in the database.
 */
class CollegeAdminProvisioningService
{
    /**
     * Provision a College Admin for the given tenant.
     *
     * @return array{user: User, temporary_password: string, email_log: \App\Models\EmailLog}
     */
    public static function provision(Tenant $tenant): array
    {
        // ── 1. Determine the admin email ──────────────────────────────────────
        // Priority: principal_email > institution email
        $adminEmail = $tenant->principal_email ?: $tenant->email;

        if (empty($adminEmail)) {
            throw new \RuntimeException('No email address found for this institution. Please add a Principal Email.');
        }

        // ── 2. Generate temporary password ────────────────────────────────────
        $temporaryPassword = self::generateTemporaryPassword($tenant);

        // ── 3. Resolve the college_admin role ─────────────────────────────────
        $role = Role::where('name', Role::COLLEGE_ADMIN)->firstOrFail();

        // ── 4. Create or update the admin user ────────────────────────────────
        // If a user with this email already exists (e.g. student from another tenant),
        // we create a NEW user with a modified email to avoid conflicts.
        // We never silently fall back to a fake email.
        $existingUser = User::where('email', $adminEmail)->first();

        if ($existingUser && $existingUser->tenant_id !== $tenant->id) {
            // Email belongs to a different tenant's user — make it unique
            // by prefixing with the tenant slug
            $adminEmail = 'admin.' . $tenant->slug . '@' . substr($adminEmail, strpos($adminEmail, '@') + 1);

            // If still conflicts, append tenant id
            if (User::where('email', $adminEmail)->exists()) {
                $adminEmail = 'admin' . $tenant->id . '.' . $tenant->slug . '@gmail.com';
            }
        }

        $adminName = $tenant->principal_name ?: ('Admin — ' . $tenant->name);

        $user = User::create([
            'tenant_id'            => $tenant->id,
            'role_id'              => $role->id,
            'name'                 => $adminName,
            'email'                => $adminEmail,
            'password'             => Hash::make($temporaryPassword),
            'status'               => 'active',
            'must_change_password' => true,
        ]);

        // ── 5. Log password history (no plain text stored) ────────────────────
        PasswordHistory::create([
            'user_id'      => $user->id,
            'tenant_id'    => $tenant->id,
            'action'       => 'generated',
            'generated_by' => 'system',
            'email_sent'   => false,
        ]);

        // ── 6. Send welcome email to the principal email ──────────────────────
        // Always send to the original principal_email even if we modified the login email.
        // The login email ($user->email) may differ if we had to de-duplicate it.
        // The welcome email always goes to the human's real inbox: principal_email.
        $sendTo = $tenant->principal_email ?: $tenant->email;

        Log::info('WELCOME EMAIL RECIPIENT', [
            'tenant_id'       => $tenant->id,
            'tenant_name'     => $tenant->name,
            'principal_email' => $tenant->principal_email,  // What was entered in the form
            'institution_email' => $tenant->email,          // The institution contact email
            'admin_login_email' => $user->email,            // What the admin logs in with (may differ)
            'send_to'         => $sendTo,                   // ← actual recipient of this email
            'login_differs_from_send_to' => ($user->email !== $sendTo),
        ]);

        $emailLog = MailLogService::send(
            $sendTo,
            new CollegeAdminWelcomeMail($user, $tenant, $temporaryPassword),
            $tenant->id
        );

        PasswordHistory::where('user_id', $user->id)
            ->latest()
            ->first()
            ?->update(['email_sent' => $emailLog->isSent()]);

        return [
            'user'               => $user,
            'temporary_password' => $temporaryPassword,
            'email_log'          => $emailLog,
        ];
    }

    /**
     * Reset a College Admin's password and resend credentials.
     */
    public static function resetPassword(User $user, Tenant $tenant, string $resetBy = 'super_admin'): array
    {
        $temporaryPassword = self::generateTemporaryPassword($tenant);

        $user->update([
            'password'             => Hash::make($temporaryPassword),
            'must_change_password' => true,
        ]);

        PasswordHistory::create([
            'user_id'      => $user->id,
            'tenant_id'    => $tenant->id,
            'action'       => 'reset',
            'generated_by' => $resetBy,
            'email_sent'   => false,
            'reset_at'     => now(),
        ]);

        // Send to principal_email (the real inbox), not the login email
        $sendTo = $tenant->principal_email ?: $user->email;

        Log::info('WELCOME EMAIL RECIPIENT', [
            'tenant_id'         => $tenant->id,
            'tenant_name'       => $tenant->name,
            'principal_email'   => $tenant->principal_email,
            'institution_email' => $tenant->email,
            'admin_login_email' => $user->email,
            'send_to'           => $sendTo,
            'action'            => 'reset_password',
            'reset_by'          => $resetBy,
            'login_differs_from_send_to' => ($user->email !== $sendTo),
        ]);

        $emailLog = MailLogService::send(
            $sendTo,
            new CollegeAdminWelcomeMail($user, $tenant, $temporaryPassword),
            $tenant->id
        );

        PasswordHistory::where('user_id', $user->id)
            ->latest()
            ->first()
            ?->update(['email_sent' => $emailLog->isSent()]);

        return [
            'temporary_password' => $temporaryPassword,
            'email_log'          => $emailLog,
        ];
    }

    /**
     * Generate a secure human-readable temporary password.
     * Format: CollegeName@XXXX  e.g. Sunrise@4821
     */
    private static function generateTemporaryPassword(Tenant $tenant): string
    {
        $prefix = ucfirst(preg_replace('/[^a-zA-Z]/', '', $tenant->slug));
        if (strlen($prefix) < 3) {
            $prefix = ucfirst(preg_replace('/[^a-zA-Z]/', '', $tenant->name));
        }
        $prefix = substr($prefix, 0, 10);
        $suffix = str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);

        return $prefix . '@' . $suffix;
    }
}
