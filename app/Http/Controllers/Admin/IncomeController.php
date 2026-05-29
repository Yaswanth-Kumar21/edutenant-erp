<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Services\TenantService;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = TenantService::getTenantId();

        $query = Income::where('tenant_id', $tenantId)
            ->with(['incomeCategory', 'recordedBy'])
            ->latest('income_date');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%{$s}%")->orWhere('reference_number', 'like', "%{$s}%"));
        }
        if ($request->filled('date_from'))   $query->whereDate('income_date', '>=', $request->date_from);
        if ($request->filled('date_to'))     $query->whereDate('income_date', '<=', $request->date_to);
        if ($request->filled('category_id')) $query->where('income_category_id', $request->category_id);

        $incomes    = $query->paginate(20)->withQueryString();
        $categories = IncomeCategory::where('tenant_id', $tenantId)->orderBy('name')->get();

        $stats = [
            'total_this_month' => Income::where('tenant_id', $tenantId)
                ->whereMonth('income_date', now()->month)->whereYear('income_date', now()->year)->sum('amount'),
            'total_this_year'  => Income::where('tenant_id', $tenantId)
                ->whereYear('income_date', now()->year)->sum('amount'),
            'count'            => Income::where('tenant_id', $tenantId)->count(),
        ];

        return view('admin.incomes.index', compact('incomes', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = IncomeCategory::where('tenant_id', TenantService::getTenantId())->get();
        return view('admin.incomes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'income_category_id' => 'required|exists:income_categories,id',
            'title'              => 'required|string|max:200',
            'amount'             => 'required|numeric|min:0',
            'income_date'        => 'required|date',
            'payment_mode'       => 'nullable|in:cash,upi,bank_transfer,cheque,online',
            'reference_number'   => 'nullable|string|max:100',
            'description'        => 'nullable|string|max:1000',
        ]);
        Income::create(array_merge($data, ['tenant_id' => TenantService::getTenantId(), 'recorded_by' => auth()->id()]));
        return redirect()->route('admin.incomes.index')->with('success', 'Income recorded successfully.');
    }

    public function show(Income $income)
    {
        return view('admin.incomes.edit', compact('income'));
    }

    public function edit(Income $income)
    {
        $categories = IncomeCategory::where('tenant_id', TenantService::getTenantId())->get();
        return view('admin.incomes.edit', compact('income', 'categories'));
    }

    public function update(Request $request, Income $income)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:200',
            'amount'           => 'required|numeric|min:0',
            'income_date'      => 'nullable|date',
            'payment_mode'     => 'nullable|in:cash,upi,bank_transfer,cheque,online',
            'reference_number' => 'nullable|string|max:100',
            'description'      => 'nullable|string|max:1000',
        ]);
        $income->update($data);
        return redirect()->route('admin.incomes.index')->with('success', 'Income updated successfully.');
    }

    public function destroy(Income $income)
    {
        $income->delete();
        return redirect()->route('admin.incomes.index')->with('success', 'Income deleted.');
    }
}
