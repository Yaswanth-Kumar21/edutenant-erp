<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\TenantService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = TenantService::getTenantId();

        $query = Expense::where('tenant_id', $tenantId)
            ->with('expenseCategory')
            ->latest('expense_date');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('title', 'like', "%{$s}%")->orWhere('vendor_name', 'like', "%{$s}%"));
        }
        if ($request->filled('date_from'))   $query->whereDate('expense_date', '>=', $request->date_from);
        if ($request->filled('date_to'))     $query->whereDate('expense_date', '<=', $request->date_to);
        if ($request->filled('category_id')) $query->where('expense_category_id', $request->category_id);

        $expenses   = $query->paginate(20)->withQueryString();
        $categories = ExpenseCategory::where('tenant_id', $tenantId)->orderBy('name')->get();

        $stats = [
            'total_this_month' => Expense::where('tenant_id', $tenantId)
                ->whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year)->sum('amount'),
            'total_this_year'  => Expense::where('tenant_id', $tenantId)
                ->whereYear('expense_date', now()->year)->sum('amount'),
            'count'            => Expense::where('tenant_id', $tenantId)->count(),
        ];

        return view('admin.expenses.index', compact('expenses', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('tenant_id', TenantService::getTenantId())->get();
        return view('admin.expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'title'               => 'required|string|max:200',
            'amount'              => 'required|numeric|min:0',
            'expense_date'        => 'required|date',
            'payment_mode'        => 'required|in:cash,online,cheque,upi',
            'bill_number'         => 'nullable|string|max:100',
            'vendor_name'         => 'nullable|string|max:200',
            'description'         => 'nullable|string|max:1000',
        ]);
        Expense::create(array_merge($data, ['tenant_id' => TenantService::getTenantId(), 'recorded_by' => auth()->id()]));
        return redirect()->route('admin.expenses.index')->with('success', 'Expense recorded successfully.');
    }

    public function show(Expense $expense)
    {
        return view('admin.expenses.edit', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $categories = \App\Models\ExpenseCategory::where('tenant_id', TenantService::getTenantId())->get();
        return view('admin.expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:200',
            'amount'       => 'required|numeric|min:0',
            'expense_date' => 'nullable|date',
            'payment_mode' => 'nullable|in:cash,online,cheque,upi',
            'bill_number'  => 'nullable|string|max:100',
            'vendor_name'  => 'nullable|string|max:200',
            'description'  => 'nullable|string|max:1000',
        ]);
        $expense->update($data);
        return redirect()->route('admin.expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('admin.expenses.index')->with('success', 'Expense deleted.');
    }
}
