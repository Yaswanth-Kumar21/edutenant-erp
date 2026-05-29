<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * StudentProfileController
 *
 * Handles the student-facing profile view and password change.
 * Students can view their profile and change their own password.
 */
class StudentProfileController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'No student profile linked to this account.');
        }

        $student->load([
            'branch.course.stream',
            'academicYear',
            'guardian',
            'profile',
        ]);

        $tenant = TenantService::getTenant();

        return view('student.profile.index', compact('user', 'student', 'tenant'));
    }

    /**
     * Show the password change form.
     */
    public function editPassword()
    {
        $user    = auth()->user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'No student profile linked to this account.');
        }

        $tenant = TenantService::getTenant();

        return view('student.profile.password', compact('user', 'student', 'tenant'));
    }

    /**
     * Update the student's password.
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

        return redirect()
            ->route('student.profile.index')
            ->with('success', 'Password changed successfully.');
    }
}
