<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ImpersonationLog;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CollegeAdminProvisioningService;
use App\Services\MailLogService;
use App\Mail\CollegeAdminWelcomeMail;
use App\Services\OnboardingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TenantController extends Controller
{
    public function index()
    {
        $query = Tenant::withCount(['students', 'staff'])->latest();

        if (request()->filled('search')) {
            $s = request('search');
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('slug', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%"));
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        $tenants = $query->paginate(15)->withQueryString();

        // Attach onboarding badge to each tenant
        $onboardingBadges = [];
        foreach ($tenants as $tenant) {
            $onboardingBadges[$tenant->id] = OnboardingService::getBadge($tenant);
        }

        return view('super-admin.tenants.index', compact('tenants', 'onboardingBadges'));
    }

    public function create()
    {
        return view('super-admin.tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:200',
            'slug'                 => 'required|string|max:100|unique:tenants',
            'email'                => 'required|email|unique:tenants',
            'principal_email'      => 'required|email|max:200',
            'phone'                => 'nullable|string|max:20',
            'principal_name'       => 'nullable|string|max:150',
            'affiliation_number'   => 'nullable|string|max:100',
            'website'              => 'nullable|url|max:200',
            'domain'               => 'nullable|string|max:200',
            'address'              => 'nullable|string|max:500',
            'city'                 => 'nullable|string|max:100',
            'state'                => 'nullable|string|max:100',
            'pincode'              => 'nullable|string|max:10',
        ]);

        $validated['status'] = 'active';

        $tenant = Tenant::create($validated);

        // ── Auto-provision College Admin ──────────────────────────────────────
        try {
            $provisioned = CollegeAdminProvisioningService::provision($tenant);

            // principal_email  = where the welcome email was delivered
            // admin_email      = what the admin types at /login (may differ if de-duplicated)
            session([
                'new_admin_credentials' => [
                    'tenant_id'          => $tenant->id,
                    'admin_email'        => $provisioned['user']->email,           // login credential
                    'admin_name'         => $provisioned['user']->name,
                    'temporary_password' => $provisioned['temporary_password'],
                    'email_sent'         => $provisioned['email_log']->isSent(),
                    'email_log_id'       => $provisioned['email_log']->id,
                    'recipient_email'    => $tenant->principal_email ?: $tenant->email, // actual inbox
                ],
            ]);

            $emailStatus = $provisioned['email_log']->isSent()
                ? "Welcome email sent to {$tenant->principal_email}"
                : "Institution created but email delivery failed — check Delivery Verification on this page";

            return redirect()
                ->route('super.tenants.show', $tenant)
                ->with('success', "Institution '{$tenant->name}' created. {$emailStatus}");

        } catch (\Throwable $e) {
            return redirect()
                ->route('super.tenants.show', $tenant)
                ->with('warning', "Institution created, but admin setup failed: {$e->getMessage()}");
        }
    }

    public function show(Tenant $tenant)
    {
        $tenant->loadCount(['students', 'staff']);

        // Get real-time onboarding status
        $onboarding = OnboardingService::getStatus($tenant);

        // Always load the actual college admin from DB
        $adminRole    = Role::where('name', Role::COLLEGE_ADMIN)->first();
        $collegeAdmin = $adminRole
            ? User::where('tenant_id', $tenant->id)
                  ->where('role_id', $adminRole->id)
                  ->first()
            : null;

        // If session credentials exist but email doesn't match DB, update session
        $creds = session('new_admin_credentials');
        if ($creds && $creds['tenant_id'] == $tenant->id && $collegeAdmin) {
            if ($creds['admin_email'] !== $collegeAdmin->email) {
                $creds['admin_email'] = $collegeAdmin->email;
                session(['new_admin_credentials' => $creds]);
            }
        }

        // ── Email Delivery Verification data (always loaded from DB) ──────────
        $lastEmailLog = \App\Models\EmailLog::where('tenant_id', $tenant->id)
            ->where('mailable', \App\Mail\CollegeAdminWelcomeMail::class)
            ->latest()
            ->first();

        $emailDelivery = [
            'recipient_email' => $tenant->principal_email ?: $tenant->email,
            'login_email'     => $collegeAdmin?->email,
            'last_sent_at'    => $lastEmailLog?->sent_at,
            'status'          => $lastEmailLog?->status ?? 'never_sent',
            'error_message'   => $lastEmailLog?->error_message,
            'log_id'          => $lastEmailLog?->id,
            'total_attempts'  => \App\Models\EmailLog::where('tenant_id', $tenant->id)
                ->where('mailable', \App\Mail\CollegeAdminWelcomeMail::class)
                ->count(),
        ];

        return view('super-admin.tenants.show', compact(
            'tenant', 'onboarding', 'collegeAdmin', 'emailDelivery'
        ));
    }

    public function edit(Tenant $tenant)
    {
        return view('super-admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:200',
            'status'               => 'required|in:active,inactive,suspended',
            'email'                => 'nullable|email|max:150',
            'principal_email'      => 'nullable|email|max:200',
            'phone'                => 'nullable|string|max:20',
            'principal_name'       => 'nullable|string|max:150',
            'affiliation_number'   => 'nullable|string|max:100',
            'website'              => 'nullable|url|max:200',
            'domain'               => 'nullable|string|max:200',
            'address'              => 'nullable|string|max:500',
            'city'                 => 'nullable|string|max:100',
            'state'                => 'nullable|string|max:100',
            'pincode'              => 'nullable|string|max:10',
        ]);

        $tenant->update($validated);

        // If principal_email changed, update the college admin user email too
        if (!empty($validated['principal_email'])) {
            $adminRole    = Role::where('name', Role::COLLEGE_ADMIN)->first();
            $collegeAdmin = $adminRole
                ? User::where('tenant_id', $tenant->id)->where('role_id', $adminRole->id)->first()
                : null;

            if ($collegeAdmin && $collegeAdmin->email !== $validated['principal_email']) {
                // Only update if the new email isn't already taken by another user
                if (!User::where('email', $validated['principal_email'])->where('id', '!=', $collegeAdmin->id)->exists()) {
                    $collegeAdmin->update(['email' => $validated['principal_email']]);
                }
            }
        }

        return redirect()
            ->route('super.tenants.show', $tenant)
            ->with('success', 'Institution updated successfully.');
    }

    public function destroy(Tenant $tenant)
    {
        $name = $tenant->name;
        $tenant->delete();
        return redirect()->route('super.tenants.index')->with('success', "'{$name}' has been deleted.");
    }

    /**
     * Impersonate a College Admin — "Login As College Admin".
     * Stores the original super admin session and logs the action.
     */
    public function impersonate(Request $request, Tenant $tenant)
    {
        $superAdmin = auth()->user();

        // Find the college admin for this tenant
        $adminRole    = Role::where('name', Role::COLLEGE_ADMIN)->firstOrFail();
        $collegeAdmin = User::where('tenant_id', $tenant->id)
                            ->where('role_id', $adminRole->id)
                            ->where('status', 'active')
                            ->first();

        if (!$collegeAdmin) {
            return back()->with('error', 'No active College Admin found for this institution.');
        }

        // Log the impersonation
        $log = ImpersonationLog::create([
            'super_admin_id'      => $superAdmin->id,
            'tenant_id'           => $tenant->id,
            'impersonated_user_id'=> $collegeAdmin->id,
            'ip_address'          => $request->ip(),
            'login_time'          => now(),
        ]);

        // Store original super admin ID in session for "stop impersonating"
        session([
            'impersonating'          => true,
            'original_super_admin_id'=> $superAdmin->id,
            'impersonation_log_id'   => $log->id,
        ]);

        // Switch auth to college admin
        Auth::login($collegeAdmin);

        return redirect()->route('dashboard')
            ->with('info', "You are now logged in as {$collegeAdmin->name} ({$tenant->name}). Use 'Stop Impersonating' to return.");
    }

    /**
     * Stop impersonating — return to Super Admin session.
     */
    public function stopImpersonating(Request $request)
    {
        if (!session('impersonating')) {
            return redirect()->route('dashboard');
        }

        $originalId = session('original_super_admin_id');
        $logId      = session('impersonation_log_id');

        // Update logout time in audit log
        if ($logId) {
            ImpersonationLog::find($logId)?->update(['logout_time' => now()]);
        }

        // Clear impersonation session data
        session()->forget(['impersonating', 'original_super_admin_id', 'impersonation_log_id']);

        // Restore original super admin
        $superAdmin = User::find($originalId);
        if ($superAdmin) {
            Auth::login($superAdmin);
            return redirect()->route('super.tenants.index')
                ->with('success', 'Impersonation ended. You are back as Super Admin.');
        }

        Auth::logout();
        return redirect()->route('login');
    }

    /**
     * Resend credentials email for a tenant's college admin.
     */
    public function resendCredentials(Request $request, Tenant $tenant)
    {
        $adminRole    = Role::where('name', Role::COLLEGE_ADMIN)->firstOrFail();
        $collegeAdmin = User::where('tenant_id', $tenant->id)
                            ->where('role_id', $adminRole->id)
                            ->first();

        if (!$collegeAdmin) {
            return back()->with('error', 'No College Admin found for this institution.');
        }

        // We can only resend with a new password since we don't store the old one
        $result = CollegeAdminProvisioningService::resetPassword(
            $collegeAdmin,
            $tenant,
            auth()->user()->name
        );

        // Store new credentials in session for display
        session([
            'new_admin_credentials' => [
                'tenant_id'          => $tenant->id,
                'admin_email'        => $collegeAdmin->email,
                'admin_name'         => $collegeAdmin->name,
                'temporary_password' => $result['temporary_password'],
                'email_sent'         => $result['email_log']->isSent(),
                'email_log_id'       => $result['email_log']->id,
                'recipient_email'    => $tenant->principal_email ?: $collegeAdmin->email,
            ],
        ]);

        $status = $result['email_log']->isSent() ? 'sent' : 'failed';
        return back()->with(
            $status === 'sent' ? 'success' : 'warning',
            $status === 'sent'
                ? "New credentials generated. Email delivered to {$tenant->principal_email}."
                : "New credentials generated but email failed: {$result['email_log']->error_message}"
        );
    }

}
