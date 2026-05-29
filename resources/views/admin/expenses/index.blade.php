@extends('layouts.app')
@section('title', 'Expenses')

@section('breadcrumb')
    <li class="breadcrumb-item active">Expenses</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-arrow-trend-down me-2" style="color:#dc2626;"></i>Expenses</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Track and manage all college expenses</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <div class="dropdown">
            <button class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fa-solid fa-file-excel me-1"></i> Export
            </button>
            <ul class="dropdown-menu shadow border-0" style="border-radius:.75rem;background:var(--surface);">
                <li>
                    <a class="dropdown-item" style="font-size:.85rem;"
                       href="{{ route('admin.exports.expenses', ['format'=>'xlsx']) }}">
                        <i class="fa-solid fa-file-excel me-2" style="color:#059669;"></i> Excel (.xlsx)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" style="font-size:.85rem;"
                       href="{{ route('admin.exports.expenses', ['format'=>'csv']) }}">
                        <i class="fa-solid fa-file-csv me-2" style="color:#0891b2;"></i> CSV
                    </a>
                </li>
            </ul>
        </div>
        <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Add Expense
        </a>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#dc2626;">?{{ number_format($stats['total_this_month'] ?? 0) }}</div>
            <div style="font-size:.78rem;color:var(--muted);">This Month</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#d97706;">?{{ number_format($stats['total_this_year'] ?? 0) }}</div>
            <div style="font-size:.78rem;color:var(--muted);">This Year</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#4f46e5;">{{ $stats['count'] ?? 0 }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Total Records</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search title, vendor..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm"
                       value="{{ request('date_from') }}" placeholder="From">
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm"
                       value="{{ request('date_to') }}" placeholder="To">
            </div>
            <div class="col-6 col-md-2">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach($categories ?? [] as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;"><i class="fa-solid fa-list me-2" style="color:#dc2626;"></i>Expense Records</span>
        <span class="badge" style="background:rgba(220,38,38,.1);color:#dc2626;">{{ $expenses->total() ?? 0 }}</span>
    </div>
    <div class="card-body p-0">
        @if(isset($expenses) && $expenses->count())
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Vendor</th>
                        <th>Amount</th>
                        <th>Mode</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $expense)
                    <tr>
                        <td style="font-size:.82rem;">{{ $expense->expense_date?->format('d M Y') }}</td>
                        <td>
                            <div style="font-weight:500;font-size:.875rem;">{{ $expense->title }}</div>
                            @if($expense->bill_number)
                            <div style="font-size:.72rem;color:var(--muted);">Bill: {{ $expense->bill_number }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge" style="background:rgba(220,38,38,.1);color:#dc2626;font-size:.72rem;">
                                {{ $expense->expenseCategory?->name ?? '—' }}
                            </span>
                        </td>
                        <td style="font-size:.82rem;">{{ $expense->vendor_name ?? '—' }}</td>
                        <td style="font-weight:700;color:#dc2626;">?{{ number_format($expense->amount, 2) }}</td>
                        <td style="font-size:.82rem;">{{ ucfirst($expense->payment_mode ?? '—') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.expenses.edit', $expense) }}"
                                   class="btn btn-sm btn-outline-secondary" style="padding:.2rem .5rem;">
                                    <i class="fa-solid fa-pen" style="font-size:.72rem;"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            style="padding:.2rem .5rem;"
                                            onclick="return confirm('Delete this expense?')">
                                        <i class="fa-solid fa-trash" style="font-size:.72rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
        <div class="d-flex justify-content-center py-3">
            {{ $expenses->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
        @else
        <div class="text-center py-5" style="color:var(--muted);">
            <i class="fa-solid fa-receipt d-block mb-2" style="font-size:2.5rem;opacity:.3;"></i>
            <div>No expense records found.</div>
            <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary btn-sm mt-3">
                <i class="fa-solid fa-plus me-1"></i> Add First Expense
            </a>
        </div>
        @endif
    </div>
</div>

@endsection

