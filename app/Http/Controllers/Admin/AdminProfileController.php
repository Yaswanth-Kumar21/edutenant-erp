<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

/**
 * AdminProfileController
 *
 * Handles profile management for all admin roles:
 * super_admin, college_admin, staff, teacher
 *
 * Routes: /admin/profile/*
 */
class AdminProfileController extends Controller
{
    /**
     * Show the profile page.
     */
    public function index()
    {
        $user   = auth()->user();
        $tenant = $user->isSuperAdmin() ? null : TenantService::getTenant();

        // Load staff/student profile if available
        $staffProfile   = $user->staffProfile?->load(['staffRole']);
        $recentActivity = $this->getRecentActivity($user);

        return view('admin.profile.index', compact('user', 'tenant', 'staffProfile', 'recentActivity'));
    }

    /**
     * Upload/update profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = auth()->user();

        // Delete old avatar if it's a stored file
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return back()->with('success', 'Profile photo updated successfully.');
    }

    /**
     * Update personal information.
     */
    public function updateInfo(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,' . $user->id],
        ]);

        if ($data['email'] !== $user->email) {
            $data['email_verified_at'] = null;
        }

        $user->update($data);

        return back()->with('success', 'Profile information updated successfully.');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password changed successfully.');
    }

    /**
     * Get recent activity for the user (last 10 login-related events).
     */
    private function getRecentActivity($user): array
    {
        // Simple activity log based on available data
        $activity = [];

        $activity[] = [
            'icon'    => 'fa-right-to-bracket',
            'color'   => '#059669',
            'title'   => 'Last Login',
            'detail'  => $user->updated_at?->format('d M Y, h:i A') ?? 'Unknown',
        ];

        $activity[] = [
            'icon'    => 'fa-user',
            'color'   => '#4f46e5',
            'title'   => 'Account Created',
            'detail'  => $user->created_at?->format('d M Y') ?? 'Unknown',
        ];

        if ($user->email_verified_at) {
            $activity[] = [
                'icon'   => 'fa-envelope-circle-check',
                'color'  => '#0891b2',
                'title'  => 'Email Verified',
                'detail' => $user->email_verified_at->format('d M Y'),
            ];
        }

        return $activity;
    }
}
