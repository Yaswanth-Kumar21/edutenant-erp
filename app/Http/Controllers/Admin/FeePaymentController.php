<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Models\FeeType;
use App\Models\Student;
use App\Services\FeeCollectionService;
use App\Services\TenantService;
use Illuminate\Http\Request;

class FeePaymentController extends Controller
{
    public function index()
    {
        $payments = FeePayment::where('tenant_id', TenantService::getTenantId())
            ->with(['student', 'feeType'])->latest('payment_date')->paginate(20);
        return view('admin.fees.payments.index', compact('payments'));
    }

    public function create()
    {
        $tenantId = TenantService::getTenantId();
        $students = Student::where('tenant_id', $tenantId)->where('status', 'active')->get();
        $feeTypes = FeeType::where('tenant_id', $tenantId)->where('is_active', true)->get();
        return view('admin.fees.payments.create', compact('students', 'feeTypes'));
    }

    public function store(Request $request)
    {
        $tenantId = TenantService::getTenantId();
        $data = $request->validate([
            'student_id'   => 'required|exists:students,id',
            'fee_type_id'  => 'required|exists:fee_types,id',
            'amount_paid'  => 'required|numeric|min:0',
            'payment_mode' => 'required|in:cash,online,cheque,dd,upi',
            'payment_date' => 'required|date',
        ]);

        // Use the locked receipt number generator to prevent duplicates
        $data['receipt_number'] = FeeCollectionService::generateReceiptNumber($tenantId);
        $data['tenant_id']      = $tenantId;
        $data['collected_by']   = auth()->id();
        $data['status']         = 'paid';
        $data['amount_due']     = $data['amount_paid'];

        $payment = FeePayment::create($data);
        return redirect()->route('admin.fees.receipt', $payment)->with('success', 'Payment recorded.');
    }

    public function show(FeePayment $payment)
    {
        return view('admin.fees.payments.show', compact('payment'));
    }

    public function edit(FeePayment $payment)   { return view('admin.fees.payments.edit', compact('payment')); }
    public function update(Request $request, FeePayment $payment) { return redirect()->route('admin.fees.payments.index'); }
    public function destroy(FeePayment $payment) { $payment->delete(); return redirect()->route('admin.fees.payments.index'); }

    public function receipt(FeePayment $payment)
    {
        $payment->load(['student.branch.course', 'feeType', 'collectedBy']);
        return view('admin.fees.receipt', compact('payment'));
    }
}
