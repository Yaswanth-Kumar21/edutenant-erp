<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeePayment;
use App\Models\Income;
use App\Models\PaymentRegister;
use App\Services\TenantService;
use Illuminate\Http\Request;

class PaymentRegisterController extends Controller
{
    /**
     * Block super admin from accessing institution-specific registers.
     */
    private function guardSuperAdmin()
    {
        if (auth()->user()->isSuperAdmin()) {
            abort(403, 'Payments Register is institution-specific. Please impersonate a college to access it.');
        }
    }

    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $this->guardSuperAdmin();

        $tenantId      = TenantService::getTenantId();
        $academicYears = AcademicYear::where('tenant_id', $tenantId)->orderByDesc('start_date')->get();
        $currentYear   = $academicYears->firstWhere('is_current', true) ?? $academicYears->first();

        $selectedYearId = $request->get('academic_year_id', $currentYear?->id);

        $query = PaymentRegister::where('tenant_id', $tenantId)
            ->with('academicYear')
            ->orderBy('entry_date');

        if ($selectedYearId) {
            $query->where('academic_year_id', $selectedYearId);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('entry_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('entry_date', '<=', $request->date_to);
        }

        $entries      = $query->paginate(25)->withQueryString();
        $selectedYear = $academicYears->firstWhere('id', $selectedYearId);

        // ── Financial Dashboard Stats ─────────────────────────────────────────

        $today     = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        // Total payments spent this year (from payment register)
        $totalSpentYear = PaymentRegister::where('tenant_id', $tenantId)
            ->when($selectedYearId, fn($q) => $q->where('academic_year_id', $selectedYearId))
            ->sum('total');

        // Yesterday's total spend
        $yesterdaySpend = PaymentRegister::where('tenant_id', $tenantId)
            ->whereDate('entry_date', $yesterday)
            ->sum('total');

        // Today's total spend
        $todaySpend = PaymentRegister::where('tenant_id', $tenantId)
            ->whereDate('entry_date', $today)
            ->sum('total');

        // Total income this year (fee payments + other income)
        $totalFeeIncome = FeePayment::where('tenant_id', $tenantId)
            ->when($selectedYearId, fn($q) => $q->where('academic_year_id', $selectedYearId))
            ->where('status', 'paid')
            ->sum('amount_paid');

        $totalOtherIncome = Income::where('tenant_id', $tenantId)
            ->when($selectedYear, function ($q) use ($selectedYear) {
                $q->whereDate('income_date', '>=', $selectedYear->start_date)
                  ->whereDate('income_date', '<=', $selectedYear->end_date);
            })
            ->sum('amount');

        $totalIncome = $totalFeeIncome + $totalOtherIncome;

        // Net profit = total income - total payments spent
        $netProfit = $totalIncome - $totalSpentYear;

        // Daily profit breakdown — last 30 days
        $dailyData = PaymentRegister::where('tenant_id', $tenantId)
            ->when($selectedYearId, fn($q) => $q->where('academic_year_id', $selectedYearId))
            ->selectRaw('entry_date, SUM(total) as day_total')
            ->groupBy('entry_date')
            ->orderBy('entry_date')
            ->get();

        // Running balance (cumulative: income - spend day by day)
        $runningBalance = [];
        $cumSpend = 0;
        foreach ($dailyData as $day) {
            $cumSpend += $day->day_total;
            $runningBalance[] = [
                'date'    => $day->entry_date,
                'spend'   => $day->day_total,
                'balance' => $totalIncome - $cumSpend,
            ];
        }

        // Grand total (for filtered/paginated display)
        $grandTotal = $totalSpentYear;

        return view('admin.payment-register.index', compact(
            'entries', 'academicYears', 'selectedYear', 'selectedYearId',
            'grandTotal', 'yesterdaySpend', 'todaySpend',
            'totalIncome', 'totalSpentYear', 'netProfit', 'runningBalance'
        ));
    }

    // ─── Create ───────────────────────────────────────────────────────────────

    public function create()
    {
        $this->guardSuperAdmin();

        $tenantId     = TenantService::getTenantId();
        $academicYears = AcademicYear::where('tenant_id', $tenantId)->orderByDesc('start_date')->get();
        $currentYear  = $academicYears->firstWhere('is_current', true) ?? $academicYears->first();
        $columns      = PaymentRegister::PAYMENT_COLUMNS;

        return view('admin.payment-register.create', compact('academicYears', 'currentYear', 'columns'));
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $tenantId = TenantService::getTenantId();

        $rules = [
            'academic_year_id' => 'required|exists:academic_years,id',
            'entry_date'       => 'required|date',
            'remarks'          => 'nullable|string|max:500',
        ];

        // Add numeric validation for each payment column
        foreach (array_keys(PaymentRegister::PAYMENT_COLUMNS) as $col) {
            $rules[$col] = 'nullable|numeric|min:0';
        }

        $data = $request->validate($rules);

        // Fill missing columns with 0
        foreach (array_keys(PaymentRegister::PAYMENT_COLUMNS) as $col) {
            $data[$col] = $data[$col] ?? 0;
        }

        $data['tenant_id']   = $tenantId;
        $data['recorded_by'] = auth()->id();

        // Compute row total
        $data['total'] = array_sum(array_map(
            fn($col) => (float) ($data[$col] ?? 0),
            array_keys(PaymentRegister::PAYMENT_COLUMNS)
        ));

        PaymentRegister::create($data);

        return redirect()->route('admin.payment-register.index')
            ->with('success', 'Payment register entry saved successfully.');
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show(PaymentRegister $paymentRegister)
    {
        $columns = PaymentRegister::PAYMENT_COLUMNS;
        return view('admin.payment-register.show', compact('paymentRegister', 'columns'));
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────

    public function edit(PaymentRegister $paymentRegister)
    {
        $tenantId     = TenantService::getTenantId();
        $academicYears = AcademicYear::where('tenant_id', $tenantId)->orderByDesc('start_date')->get();
        $columns      = PaymentRegister::PAYMENT_COLUMNS;

        return view('admin.payment-register.edit', compact('paymentRegister', 'academicYears', 'columns'));
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(Request $request, PaymentRegister $paymentRegister)
    {
        $rules = [
            'academic_year_id' => 'required|exists:academic_years,id',
            'entry_date'       => 'required|date',
            'remarks'          => 'nullable|string|max:500',
        ];

        foreach (array_keys(PaymentRegister::PAYMENT_COLUMNS) as $col) {
            $rules[$col] = 'nullable|numeric|min:0';
        }

        $data = $request->validate($rules);

        foreach (array_keys(PaymentRegister::PAYMENT_COLUMNS) as $col) {
            $data[$col] = $data[$col] ?? 0;
        }

        // Recompute total on update
        $data['total'] = array_sum(array_map(
            fn($col) => (float) ($data[$col] ?? 0),
            array_keys(PaymentRegister::PAYMENT_COLUMNS)
        ));

        $paymentRegister->update($data);

        return redirect()->route('admin.payment-register.index')
            ->with('success', 'Payment register entry updated successfully.');
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(PaymentRegister $paymentRegister)
    {
        $paymentRegister->delete();

        return redirect()->route('admin.payment-register.index')
            ->with('success', 'Entry deleted successfully.');
    }

    // ─── Report (printable full-width ledger) ─────────────────────────────────

    public function report(Request $request)
    {
        $tenantId     = TenantService::getTenantId();
        $tenant       = auth()->user()->tenant;
        $academicYears = AcademicYear::where('tenant_id', $tenantId)->orderByDesc('start_date')->get();
        $currentYear  = $academicYears->firstWhere('is_current', true) ?? $academicYears->first();

        $selectedYearId = $request->get('academic_year_id', $currentYear?->id);
        $selectedYear   = $academicYears->firstWhere('id', $selectedYearId);

        $entries = PaymentRegister::where('tenant_id', $tenantId)
            ->where('academic_year_id', $selectedYearId)
            ->orderBy('entry_date')
            ->get();

        // Column-wise totals for the footer row
        $columnTotals = [];
        foreach (array_keys(PaymentRegister::PAYMENT_COLUMNS) as $col) {
            $columnTotals[$col] = $entries->sum($col);
        }
        $grandTotal = $entries->sum('total');
        $columns    = PaymentRegister::PAYMENT_COLUMNS;

        return view('admin.payment-register.report', compact(
            'entries', 'tenant', 'selectedYear', 'academicYears',
            'selectedYearId', 'columns', 'columnTotals', 'grandTotal'
        ));
    }
}
