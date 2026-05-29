<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Services\TenantService;
use App\Traits\TenantScoped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api as RazorpayApi;

/**
 * PaymentGatewayController
 *
 * Handles Razorpay online fee payment integration.
 * Supports: order creation, payment verification, webhook handling.
 *
 * Configuration (add to .env):
 *   RAZORPAY_KEY_ID=rzp_test_xxxx
 *   RAZORPAY_KEY_SECRET=xxxx
 */
class PaymentGatewayController extends Controller
{
    use TenantScoped;

    private function razorpay(): RazorpayApi
    {
        return new RazorpayApi(
            config('services.razorpay.key_id'),
            config('services.razorpay.key_secret')
        );
    }

    /**
     * Show the online payment page for a fee payment record.
     * GET /admin/payments/{payment}/pay-online
     */
    public function showPaymentPage(FeePayment $payment)
    {
        $this->assertTenant($payment);
        $payment->load(['student.branch', 'feeType', 'academicYear']);
        $tenant = $this->tenant();

        return view('admin.fees.payments.pay-online', compact('payment', 'tenant'));
    }

    /**
     * Create a Razorpay order.
     * POST /admin/payments/{payment}/create-order
     */
    public function createOrder(Request $request, FeePayment $payment)
    {
        $this->assertTenant($payment);

        if ($payment->status === 'paid') {
            return response()->json(['error' => 'This payment is already completed.'], 400);
        }

        $amountPaise = (int) round($payment->balance * 100); // Razorpay uses paise

        if ($amountPaise <= 0) {
            return response()->json(['error' => 'No balance due.'], 400);
        }

        try {
            $order = $this->razorpay()->order->create([
                'amount'          => $amountPaise,
                'currency'        => 'INR',
                'receipt'         => $payment->receipt_number,
                'notes'           => [
                    'fee_payment_id' => $payment->id,
                    'student_name'   => $payment->student?->full_name,
                    'tenant_id'      => $payment->tenant_id,
                ],
            ]);

            return response()->json([
                'order_id'   => $order->id,
                'amount'     => $amountPaise,
                'currency'   => 'INR',
                'key_id'     => config('services.razorpay.key_id'),
                'name'       => $this->tenant()->name,
                'description' => "Fee Payment — {$payment->feeType?->name}",
                'prefill'    => [
                    'name'  => $payment->student?->full_name,
                    'email' => $payment->student?->email ?? '',
                    'contact' => $payment->student?->phone ?? '',
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Payment gateway error. Please try again.'], 500);
        }
    }

    /**
     * Verify payment after Razorpay callback.
     * POST /admin/payments/{payment}/verify
     */
    public function verifyPayment(Request $request, FeePayment $payment)
    {
        $this->assertTenant($payment);

        $request->validate([
            'razorpay_order_id'   => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature'  => ['required', 'string'],
        ]);

        try {
            $this->razorpay()->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature,
            ]);

            // Signature verified — update payment record
            $payment->update([
                'amount_paid'           => $payment->amount_due,
                'payment_mode'          => 'online',
                'transaction_reference' => $request->razorpay_payment_id,
                'payment_date'          => now()->toDateString(),
                'status'                => 'paid',
                'remarks'               => 'Paid via Razorpay. Order: ' . $request->razorpay_order_id,
            ]);

            return redirect()
                ->route('admin.fees.payments.receipt', $payment)
                ->with('success', 'Payment successful! Receipt: ' . $payment->receipt_number);

        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            Log::warning('Razorpay signature verification failed', [
                'payment_id' => $payment->id,
                'error'      => $e->getMessage(),
            ]);
            return redirect()
                ->back()
                ->with('error', 'Payment verification failed. Please contact support.');
        }
    }

    /**
     * Handle Razorpay webhook events.
     * POST /webhook/razorpay
     */
    public function webhook(Request $request)
    {
        $webhookSecret = config('services.razorpay.webhook_secret');
        $signature     = $request->header('X-Razorpay-Signature');
        $payload       = $request->getContent();

        if ($webhookSecret) {
            $expectedSig = hash_hmac('sha256', $payload, $webhookSecret);
            if (!hash_equals($expectedSig, $signature ?? '')) {
                Log::warning('Razorpay webhook signature mismatch');
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        }

        $event = $request->input('event');
        $data  = $request->input('payload.payment.entity', []);

        Log::info('Razorpay webhook received', ['event' => $event]);

        if ($event === 'payment.failed') {
            $receiptNo = $data['notes']['receipt'] ?? null;
            if ($receiptNo) {
                FeePayment::where('receipt_number', $receiptNo)
                    ->where('status', '!=', 'paid')
                    ->update(['remarks' => 'Online payment failed. Razorpay ID: ' . ($data['id'] ?? '')]);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
