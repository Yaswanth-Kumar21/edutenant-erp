<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * PasswordChangeController
 *
 * Handles the forced first-login password change for College Admins.
 * After changing their password, they are redirected to the dashboard.
 */
class PasswordChangeController extends Controller
{
    /**
     * Show the forced password change form.
     */
    public function show()
    {
        $user = auth()->user();

        // If password change is not required, redirect to dashboard
        if (!$user->must_change_password) {
            return redirect()->route('dashboard');
        }

        return view('auth.change-password');
    }

    /**
     * Process the password change.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password'      => 'required|string',
            'password'              => ['required', 'confirmed', Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
            ],
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        // Ensure new password is different from current
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Your new password must be different from your current password.',
            ]);
        }

        // Update password and clear the force-change flag
        $user->update([
            'password'             => Hash::make($request->password),
            'must_change_password' => false,
            'password_changed_at'  => now(),
        ]);

        // Log the password change
        PasswordHistory::create([
            'user_id'      => $user->id,
            'tenant_id'    => $user->tenant_id,
            'action'       => 'changed',
            'generated_by' => $user->name,
            'email_sent'   => false,
            'reset_at'     => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Password changed successfully. Welcome to EduTenant ERP!');
    }
}
