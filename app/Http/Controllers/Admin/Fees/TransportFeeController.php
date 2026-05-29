<?php

namespace App\Http\Controllers\Admin\Fees;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\FeePayment;
use App\Models\FeeType;
use App\Models\Student;
use App\Services\FeeCollectionService;
use App\Traits\TenantScoped;
use Illuminate\Http\Request;

class TransportFeeController extends Controller
{
    use TenantScoped;

    public function __construct(
        private readonly FeeCollectionService $service
    ) {}

    public function index(Request $request)
    {
        $tenantId = $this->tenantId();

        $query = Student::where('tenant_id', $tenantId)
            ->where('vehicle_opted', true)
            ->where('status', 'active')
            ->with(['branch.course', 'feePayments' => fn($q) =>
                $q->whereHas('feeType', fn($ft) => $ft->where('code', 'VEHICLE'))
                  ->latest('payment_date')
                  ->limit(3)
            ]);

        if ($request->filled('branch_id')) $query->where('branch_id', $request->branch_id);

        $students       = $query->orderBy('first_name')->paginate(25)->withQueryString();
        $branches       = Branch::where('tenant_id', $tenantId)->with('course')->orderBy('name')->get();
        $vehicleFeeType = FeeType::where('tenant_id', $tenantId)->where('code', 'VEHICLE')->first();

        return view('admin.fees.transport.index', compact(
            'students', 'branches', 'vehicleFeeType'
        ));
    }

    public function collect(Request $request, Student $student)
    {
        $this->assertTenant($student);

        $request->validate([
            'month'            => ['required', 'integer', 'min:1', 'max:12'],
            'year'             => ['required', 'integer', 'min:2020'],
            'amount_paid'      => ['required', 'numeric', 'min:0'],
            'payment_mode'     => ['required', 'in:cash,upi,card,bank_transfer,cheque,dd'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
        ]);

        $tenantId       = $this->tenantId();
        $vehicleFeeType = FeeType::where('tenant_id', $tenantId)->where('code', 'VEHICLE')->firstOrFail();

        $data = array_merge($request->validated(), [
            'student_id'  => $student->id,
            'fee_type_id' => $vehicleFeeType->id,
            'amount_due'  => $vehicleFeeType->amount,
            'semester'    => null,
        ]);

        $payment = $this->service->collectPayment($data, $tenantId, auth()->id());

        return back()->with('success', "Transport fee collected. Receipt: {$payment->receipt_number}");
    }

    public function summary(Request $request)
    {
        $tenantId = $this->tenantId();
        $month    = $request->get('month', now()->month);
        $year     = $request->get('year', now()->year);

        $summary = FeePayment::where('tenant_id', $tenantId)
            ->whereHas('feeType', fn($q) => $q->where('code', 'VEHICLE'))
            ->whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->with(['student.branch'])
            ->get();

        $totalCollected = $summary->where('status', 'paid')->sum('amount_paid');
        $totalStudents  = Student::where('tenant_id', $tenantId)
            ->where('vehicle_opted', true)
            ->where('status', 'active')
            ->count();

        return view('admin.fees.transport.summary', compact(
            'summary', 'totalCollected', 'totalStudents', 'month', 'year'
        ));
    }
}
