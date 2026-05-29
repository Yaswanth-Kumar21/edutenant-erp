<?php

namespace App\Http\Controllers\Admin\Fees;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Traits\TenantScoped;

class FeeReceiptController extends Controller
{
    use TenantScoped;

    public function show(FeePayment $payment)
    {
        $this->assertTenant($payment);
        $payment->load(['student.branch.course.stream', 'feeType', 'academicYear', 'collectedBy']);
        $tenant = $this->tenant();

        return view('admin.fees.payments.receipt', compact('payment', 'tenant'));
    }

    public function print(FeePayment $payment)
    {
        $this->assertTenant($payment);
        $payment->load(['student.branch.course.stream', 'feeType', 'academicYear', 'collectedBy']);
        $tenant = $this->tenant();

        return view('admin.fees.payments.receipt-print', compact('payment', 'tenant'));
    }
}
