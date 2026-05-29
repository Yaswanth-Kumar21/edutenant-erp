<?php

namespace App\Http\Controllers\Admin\Fees;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Traits\TenantScoped;
use Illuminate\Http\Request;

class FeeExemptionController extends Controller
{
    use TenantScoped;

    public function index(Request $request)
    {
        $tenantId   = $this->tenantId();
        $exemptions = FeePayment::where('tenant_id', $tenantId)
            ->where('is_exempted', true)
            ->with(['student', 'feeType', 'exemptedBy'])
            ->latest()
            ->paginate(20);

        return view('admin.fees.exemptions.index', compact('exemptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'payment_id'       => ['required', 'exists:fee_payments,id'],
            'exemption_reason' => ['required', 'string', 'max:500'],
        ]);

        $payment = FeePayment::where('tenant_id', $this->tenantId())
            ->findOrFail($request->payment_id);

        $payment->update([
            'is_exempted'      => true,
            'exemption_reason' => $request->exemption_reason,
            'exempted_by'      => auth()->id(),
            'status'           => 'exempted',
        ]);

        return back()->with('success', 'Fee exemption granted.');
    }

    public function destroy(FeePayment $payment)
    {
        $this->assertTenant($payment);

        $payment->update([
            'is_exempted'      => false,
            'exemption_reason' => null,
            'exempted_by'      => null,
            'status'           => 'pending',
        ]);

        return back()->with('success', 'Fee exemption revoked.');
    }
}
