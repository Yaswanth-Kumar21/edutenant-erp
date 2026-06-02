<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PasswordHistory;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\CollegeAdminProvisioningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class TenantUserController extends Controller
{
    /**
     * Show the form to create / manage a college admin for a tenant.
     */
    public function create(Tenant $tenant)
    {
        $collegeAdminRole = Role::where('name', Role::COLLEGE_ADMIN)->firstOrFail();
        $existingAdmin    = User::where('tenant_id', $tenant->id)
                                ->where('role_id', $collegeAdminRole->id)
                                ->first();

        return view('super-admin.tenants.add-admin', compact('tenant', 'existingAdmin'));
    }

    /**
     * Manually create a college admin (used when auto-provisioning was skipped).
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:150',
            'email'    => 'required|email|max:150|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $collegeAdminRole = Role::where('name', Role::COLLEGE_ADMIN)->firstOrFail();

        $user = User::create([
            'tenant_id'            => $tenant->id,
            'role_id'              => $collegeAdminRole->id,
            'name'                 => $validated['name'],
            'email'                => $validated['email'],
            'phone'                => $validated['phone'] ?? null,
            'password'             => Hash::make($validated['password']),
            'status'               => 'active',
            'must_change_password' => true,
        ]);

        PasswordHistory::create([
            'user_id'      => $user->id,
            'tenant_id'    => $tenant->id,
            'action'       => 'generated',
            'generated_by' => auth()->user()->name,
            'email_sent'   => false,
        ]);

        return redirect()
            ->route('super.tenants.show', $tenant)
            ->with('success', "College Admin '{$validated['name']}' created successfully.");
    }

    /**
     * Reset the college admin's password via the provisioning service.
     * Generates a new temporary password and sends a welcome email.
     */
    public function resetPassword(Request $request, Tenant $tenant, User $user)
    {
        abort_if($user->tenant_id !== $tenant->id, 403, 'Unauthorized');

        $result = CollegeAdminProvisioningService::resetPassword(
            $user,
            $tenant,
            auth()->user()->name
        );

        // Store new credentials in session for one-time display
        session([
            'new_admin_credentials' => [
                'tenant_id'          => $tenant->id,
                'admin_email'        => $user->email,
                'admin_name'         => $user->name,
                'temporary_password' => $result['temporary_password'],
                'email_sent'         => $result['email_log']->isSent(),
            ],
        ]);

        return redirect()
            ->route('super.tenants.show', $tenant)
            ->with('success', 'Password reset. New credentials sent to ' . $user->email);
    }
}
