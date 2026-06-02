<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — EduTenant ERP
|--------------------------------------------------------------------------
*/

// ── Root Redirect ─────────────────────────────────────────────────────────────
Route::get('/', function () {
    if (!auth()->check()) return redirect()->route('login');
    $user = auth()->user();
    if ($user->isSuperAdmin()) return redirect()->route('dashboard');
    if ($user->isStudent())    return redirect()->route('student.dashboard');
    return redirect()->route('dashboard');
});

// ── Auth Routes (Laravel Breeze) ──────────────────────────────────────────────
require __DIR__ . '/auth.php';

// ── Authenticated Routes ──────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // ── Forced Password Change (College Admin first login) ────────────────
    Route::get('/change-password', [\App\Http\Controllers\Auth\PasswordChangeController::class, 'show'])
        ->name('password.change.show');
    Route::post('/change-password', [\App\Http\Controllers\Auth\PasswordChangeController::class, 'update'])
        ->name('password.change.update');

    // ── Dashboard Routes (role-based) ─────────────────────────────────────
    require __DIR__ . '/modules/dashboard.php';

    // ── Profile & Settings — all authenticated roles (no tenant required for super admin) ──
    Route::prefix('admin')->name('admin.')->group(function () {
        require __DIR__ . '/modules/profile.php';
    });

    // ── Admin Panel — college_admin, staff, teacher only ─────────────────
    Route::middleware(['tenant', 'role:college_admin,staff,teacher', 'force.password.change'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            require __DIR__ . '/modules/setup.php';
            require __DIR__ . '/modules/admission.php';
            require __DIR__ . '/modules/students.php';
            require __DIR__ . '/modules/fees.php';
            require __DIR__ . '/modules/attendance.php';
            require __DIR__ . '/modules/staff.php';
            require __DIR__ . '/modules/finance.php';
            require __DIR__ . '/modules/reports.php';
            require __DIR__ . '/modules/notifications.php';
            require __DIR__ . '/modules/exports.php';
        });

    // ── Super Admin Routes ────────────────────────────────────────────────
    require __DIR__ . '/modules/tenant.php';
});

// ── Razorpay Webhook (no auth, no CSRF) ──────────────────────────────────────
Route::post('/webhook/razorpay', [\App\Http\Controllers\Admin\PaymentGatewayController::class, 'webhook'])
    ->name('webhook.razorpay')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// ── Test Email AJAX (auth required) ──────────────────────────────────────────
Route::middleware(['auth'])->post('/test-email-ajax', function (\Illuminate\Http\Request $request) {
    $to = trim($request->input('to', ''));

    if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return response()->json(['status' => 'failed', 'message' => 'Invalid email address.'], 422);
    }

    try {
        \Illuminate\Support\Facades\Mail::raw(
            "EduTenant ERP — Email Delivery Test\n\n" .
            "This test email confirms that the address {$to} can receive emails from EduTenant ERP.\n\n" .
            "If you received this, your Principal Email is correctly configured and the welcome email " .
            "with login credentials will be delivered here when the institution is created.\n\n" .
            "Sent at: " . now()->format('d M Y H:i:s') . "\n" .
            "From: EduTenant ERP (" . config('mail.from.address') . ")",
            function ($msg) use ($to) {
                $msg->to($to)->subject('EduTenant ERP — Inbox Verification Test ' . now()->format('H:i:s'));
            }
        );
        \Illuminate\Support\Facades\Log::info('[test-email-ajax] Test email sent', ['to' => $to, 'by' => auth()->id()]);
        return response()->json(['status' => 'success', 'message' => "Test email sent to {$to}. Check inbox."]);
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::error('[test-email-ajax] Failed', ['to' => $to, 'error' => $e->getMessage()]);
        return response()->json(['status' => 'failed', 'message' => $e->getMessage()], 500);
    }
})->name('test.email.ajax');

// ── Debug Email Delivery (auth + super_admin only) ────────────────────────────
Route::middleware(['auth'])->get('/debug-email-delivery', function () {
    // SMTP configuration
    $smtpConfig = [
        'mailer'       => config('mail.default'),
        'host'         => config('mail.mailers.smtp.host'),
        'port'         => config('mail.mailers.smtp.port'),
        'username'     => config('mail.mailers.smtp.username'),
        'from_address' => config('mail.from.address'),
        'from_name'    => config('mail.from.name'),
        'encryption'   => config('mail.mailers.smtp.scheme') ?? 'STARTTLS (port 587)',
        'queue_driver' => config('queue.default'),
    ];

    // TCP connectivity check
    $host   = config('mail.mailers.smtp.host');
    $port   = (int) config('mail.mailers.smtp.port');
    $socket = @fsockopen("tcp://{$host}", $port, $errno, $errstr, 6);
    $tcpOk  = (bool) $socket;
    if ($socket) fclose($socket);

    // Last 20 email logs for CollegeAdminWelcomeMail
    $recentLogs = \Illuminate\Support\Facades\DB::table('email_logs')
        ->orderByDesc('id')
        ->take(20)
        ->get(['id','tenant_id','recipient','subject','status','error_message','sent_at','created_at'])
        ->toArray();

    // Queue status
    $queueStatus = [
        'pending_jobs'         => \Illuminate\Support\Facades\DB::table('jobs')->count(),
        'failed_jobs'          => \Illuminate\Support\Facades\DB::table('failed_jobs')->count(),
        'welcome_mail_queued'  => \Illuminate\Support\Facades\DB::table('jobs')
                                    ->where('payload','like','%CollegeAdminWelcomeMail%')->count(),
    ];

    // Mailable sync check
    $mailableSync = !in_array(
        'Illuminate\Bus\Queueable',
        class_uses_recursive(\App\Mail\CollegeAdminWelcomeMail::class)
    );

    // Live send test (to from_address itself as self-test)
    $testTo     = request('test_to', config('mail.from.address'));
    $testResult = null;
    if (request()->has('send_test')) {
        try {
            \Illuminate\Support\Facades\Mail::raw(
                "EduTenant debug-email-delivery test\nSent at: " . now(),
                fn($m) => $m->to($testTo)->subject('[Debug] EduTenant SMTP Test ' . now()->format('H:i:s'))
            );
            $testResult = ['status' => 'success', 'to' => $testTo];
        } catch (\Throwable $e) {
            $testResult = ['status' => 'failed', 'to' => $testTo, 'error' => $e->getMessage()];
        }
    }

    // Recent tenant principal_email vs log recipient comparison
    $tenantAudit = \Illuminate\Support\Facades\DB::table('tenants')
        ->orderByDesc('id')->take(10)
        ->get(['id','name','email','principal_email'])
        ->map(function ($t) {
            $log = \Illuminate\Support\Facades\DB::table('email_logs')
                ->where('tenant_id', $t->id)
                ->where('mailable','like','%CollegeAdminWelcomeMail%')
                ->latest()->first(['recipient','status','sent_at']);
            return [
                'tenant_id'       => $t->id,
                'institution'     => $t->name,
                'principal_email' => $t->principal_email ?? '(none — uses institution email)',
                'institution_email'=> $t->email,
                'email_sent_to'   => $log->recipient ?? 'never sent',
                'delivery_status' => $log->status    ?? 'never_sent',
                'sent_at'         => $log->sent_at   ?? null,
                'correct_recipient'=> $log ? ($log->recipient === ($t->principal_email ?: $t->email)) : null,
            ];
        })->toArray();

    return response()->json([
        'debug_email_delivery'   => true,
        'generated_at'           => now()->toDateTimeString(),
        'smtp_config'            => $smtpConfig,
        'tcp_connectivity'       => ['host' => "{$host}:{$port}", 'reachable' => $tcpOk ? 'YES' : 'NO'],
        'mailable_is_synchronous'=> $mailableSync ? 'YES (correct)' : 'NO — Queueable trait present',
        'queue_status'           => $queueStatus,
        'test_send_result'       => $testResult,
        'tenant_email_audit'     => $tenantAudit,
        'recent_email_logs'      => $recentLogs,
        'how_to_use'             => [
            'send_test_email'    => 'GET /debug-email-delivery?send_test=1&test_to=your@email.com',
            'view_logs_only'     => 'GET /debug-email-delivery',
        ],
    ]);
})->name('debug.email.delivery');
