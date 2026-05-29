<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * API AuthController
 *
 * Handles mobile API authentication using Laravel Sanctum tokens.
 * Tenant-isolated: students can only access their own tenant's data.
 */
class AuthController extends Controller
{
    /**
     * POST /api/v1/login
     * Returns a Sanctum token on success.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)
            ->with(['role', 'tenant', 'student'])
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'Your account is inactive. Please contact the college admin.',
            ], 403);
        }

        if ($user->tenant && !$user->tenant->isActive()) {
            return response()->json([
                'message' => 'Your institution account is inactive.',
            ], 403);
        }

        // Revoke old tokens for this device (optional: keep multi-device)
        $user->tokens()->where('name', 'mobile-app')->delete();

        $token = $user->createToken('mobile-app', ['*'], now()->addDays(30))->plainTextToken;

        return response()->json([
            'token'      => $token,
            'token_type' => 'Bearer',
            'expires_in' => 30 * 24 * 60 * 60, // 30 days in seconds
            'user'       => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role?->name,
                'avatar_url' => $user->avatar_url,
                'tenant'     => $user->tenant ? [
                    'id'   => $user->tenant->id,
                    'name' => $user->tenant->name,
                    'slug' => $user->tenant->slug,
                ] : null,
            ],
        ]);
    }

    /**
     * POST /api/v1/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    /**
     * GET /api/v1/me
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['role', 'tenant', 'student.branch.course']);

        return response()->json([
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'phone'      => $user->phone,
            'role'       => $user->role?->name,
            'avatar_url' => $user->avatar_url,
            'tenant'     => $user->tenant ? [
                'id'    => $user->tenant->id,
                'name'  => $user->tenant->name,
                'email' => $user->tenant->email,
                'phone' => $user->tenant->phone,
            ] : null,
            'student'    => $user->student ? [
                'id'               => $user->student->id,
                'admission_number' => $user->student->admission_number,
                'full_name'        => $user->student->full_name,
                'branch'           => $user->student->branch?->name,
                'course'           => $user->student->branch?->course?->name,
                'semester'         => $user->student->current_semester,
                'status'           => $user->student->status,
            ] : null,
        ]);
    }
}
