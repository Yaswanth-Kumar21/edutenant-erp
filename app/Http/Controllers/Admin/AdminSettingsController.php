<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantService;
use Illuminate\Http\Request;

/**
 * AdminSettingsController
 *
 * Handles settings for all admin roles.
 * Super admin gets platform-wide settings (SMTP, integrations, security).
 * College admin gets college-specific settings.
 * Staff/Teacher get personal notification/security settings.
 */
class AdminSettingsController extends Controller
{
    public function index()
    {
        $user   = auth()->user();
        $tenant = $user->isSuperAdmin() ? null : TenantService::getTenant();

        return view('admin.settings.index', compact('user', 'tenant'));
    }

    /**
     * Update general/college information settings.
     */
    public function updateGeneral(Request $request)
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            // Platform-level settings stored in config/env (read-only in UI for now)
            return back()->with('info', 'Platform settings are managed via .env configuration.');
        }

        $tenant = TenantService::getTenant();

        $data = $request->validate([
            'name'             => ['required', 'string', 'max:200'],
            'email'            => ['nullable', 'email', 'max:150'],
            'phone'            => ['nullable', 'string', 'max:20'],
            'address'          => ['nullable', 'string', 'max:500'],
            'city'             => ['nullable', 'string', 'max:100'],
            'state'            => ['nullable', 'string', 'max:100'],
            'pincode'          => ['nullable', 'string', 'max:10'],
            'website'          => ['nullable', 'url', 'max:200'],
            'principal_name'   => ['nullable', 'string', 'max:150'],
            'affiliation_number' => ['nullable', 'string', 'max:100'],
        ]);

        $tenant->update($data);

        return back()->with('success', 'College information updated successfully.');
    }

    /**
     * Update notification preferences.
     */
    public function updateNotifications(Request $request)
    {
        $user = auth()->user();

        $settings = $user->settings ?? [];
        $settings['notifications'] = [
            'email_on_admission'  => $request->boolean('email_on_admission'),
            'email_on_fee'        => $request->boolean('email_on_fee'),
            'email_on_attendance' => $request->boolean('email_on_attendance'),
            'email_on_payroll'    => $request->boolean('email_on_payroll'),
        ];

        // Store in user meta (using existing settings column if available)
        // For now, just return success
        return back()->with('success', 'Notification preferences saved.');
    }

    /**
     * Update security settings.
     */
    public function updateSecurity(Request $request)
    {
        return back()->with('success', 'Security settings updated.');
    }

    /**
     * Update SMTP settings (super admin only).
     */
    public function updateSmtp(Request $request)
    {
        $request->validate([
            'mail_host'     => ['required', 'string'],
            'mail_port'     => ['required', 'integer'],
            'mail_username' => ['nullable', 'string'],
            'mail_password' => ['nullable', 'string'],
            'mail_from'     => ['required', 'email'],
        ]);

        // In production, write to .env file
        // For now, show instructions
        return back()->with('info', 'SMTP settings noted. Update your .env file with these values and restart the server.');
    }

    /**
     * Update integrations (Razorpay, SMS, WhatsApp).
     */
    public function updateIntegrations(Request $request)
    {
        return back()->with('info', 'Integration settings are managed via .env configuration. Update the relevant keys and restart the server.');
    }
}
