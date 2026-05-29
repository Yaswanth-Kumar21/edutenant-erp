<?php

namespace App\Http\Controllers\Admin\Fees;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Fees\StoreFeePaymentRequest;
use App\Models\AcademicYear;
use App\Models\FeePayment;
use App\Models\FeeType;
use App\Models\Student;
use App\Services\FeeCollectionService;
use App\Traits\TenantScoped;
use Illuminate\Http\Request;

/**
 * FeeCollectionController
 *
 * Handles fee payment collection — create, view, receipt.
 */
class FeeCollectionController extends Controller
{
    use TenantScoped;

    public function __construct(
        private readonly FeeCollectionService $service
    ) {}

    public function index(Request $request)
    {
        $tenantId = $this->tenantId();

        $query = FeePayment::where('tenant_id', $tenantId)
            ->with(['student', 'feeType', 'academicYear']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', fn($q) =>
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%")
            )->orWhere('receipt_number', 'like', "%{$search}%");
        }

        if ($request->filled('fee_type_id'))      $query->where('fee_type_id', $request->fee_type_id);
        if ($request->filled('status'))            $query->where('status', $request->status);
        if ($request->filled('payment_mode'))      $query->where('payment_mode', $request->payment_mode);
        if ($request->filled('date_from'))         $query->whereDate('payment_date', '>=', $request->date_from);
        if ($request->filled('date_to'))           $query->whereDate('payment_date', '<=', $request->date_to);
        if ($request->filled('academic_year_id'))  $query->where('academic_year_id', $request->academic_year_id);

        $payments      = $query->latest('payment_date')->paginate(20)->withQueryString();
        $feeTypes      = FeeType::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $academicYears = AcademicYear::where('tenant_id', $tenantId)->orderByDesc('is_current')->get();

        $totals = [
            'collected' => (clone $query)->where('status', 'paid')->sum('amount_paid'),
            'pending'   => (clone $query)->whereIn('status', ['pending', 'partial'])->sum('amount_due'),
            'count'     => $payments->total(),
        ];

        return view('admin.fees.payments.index', compact(
            'payments', 'feeTypes', 'academicYears', 'totals'
        ));
    }

    public function create(Request $request)
    {
        $tenantId = $this->tenantId();

        $student = null;
        if ($request->filled('student_id')) {
            $student = Student::where('tenant_id', $tenantId)
                ->with('branch.course')
                ->find($request->student_id);
        }

        $students      = Student::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'admission_number']);

        $feeTypes      = FeeType::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('sort_order')->get();
        $academicYears = AcademicYear::where('tenant_id', $tenantId)->orderByDesc('is_current')->get();
        $currentYear   = $academicYears->firstWhere('is_current', true);

        return view('admin.fees.payments.create', compact(
            'student', 'students', 'feeTypes', 'academicYears', 'currentYear'
        ));
    }

    public function store(StoreFeePaymentRequest $request)
    {
        $payment = $this->service->collectPayment(
            $request->validated(),
            $this->tenantId(),
            auth()->id()
        );

        return redirect()
            ->route('admin.fees.payments.receipt', $payment)
            ->with('success', "Payment recorded. Receipt: {$payment->receipt_number}");
    }

    public function show(FeePayment $payment)
    {
        $this->assertTenant($payment);
        $payment->load(['student.branch.course', 'feeType', 'academicYear', 'collectedBy']);

        return view('admin.fees.payments.show', compact('payment'));
    }

    public function edit(FeePayment $payment)
    {
        $this->assertTenant($payment);
        $payment->load(['student', 'feeType']);

        return view('admin.fees.payments.edit', compact('payment'));
    }

    public function update(Request $request, FeePayment $payment)
    {
        $this->assertTenant($payment);

        $request->validate([
            'payment_mode'          => ['required', 'in:cash,upi,card,bank_transfer,cheque,dd'],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'remarks'               => ['nullable', 'string', 'max:500'],
        ]);

        $payment->update($request->only(['payment_mode', 'transaction_reference', 'remarks']));

        return redirect()
            ->route('admin.fees.payments.show', $payment)
            ->with('success', 'Payment updated.');
    }

    public function receipt(FeePayment $payment)
    {
        $this->assertTenant($payment);
        $payment->load(['student.branch.course.stream', 'feeType', 'academicYear', 'collectedBy']);
        $tenant = $this->tenant();

        return view('admin.fees.payments.receipt', compact('payment', 'tenant'));
    }

    public function printReceipt(FeePayment $payment)
    {
        $this->assertTenant($payment);
        $payment->load(['student.branch.course.stream', 'feeType', 'academicYear', 'collectedBy']);
        $tenant = $this->tenant();

        return view('admin.fees.payments.receipt-print', compact('payment', 'tenant'));
    }
}
