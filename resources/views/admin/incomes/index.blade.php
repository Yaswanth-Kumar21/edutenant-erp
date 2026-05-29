@extends('layouts.app')
@section('title', 'Income')

@section('breadcrumb')
    <li class="breadcrumb-item active">Income</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-arrow-trend-up me-2" style="color:#059669;"></i>Income</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Track all college income and revenue</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <div class="dropdown">
            <button class="btn btn-outline-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fa-solid fa-file-excel me-1"></i> Export
            </button>
            <ul class="dropdown-menu shadow border-0" style="border-radius:.75rem;background:var(--surface);">
                <li><a class="dropdown-item" style="font-size:.85rem;" href="{{ route('admin.exports.income', ['format'=>'xlsx']) }}"><i class="fa-solid fa-file-excel me-2" style="color:#059669;"></i> Excel (.xlsx)</a></li>
                <li><a class="dropdown-item" style="font-size:.85rem;" href="{{ route('admin.exports.income', ['format'=>'csv']) }}"><i class="fa-solid fa-file-csv me-2" style="color:#0891b2;"></i> CSV</a></li>
            </ul>
        </div>
        <a href="{{ route('admin.incomes.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Add Income
        </a>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#059669;">?{{ number_format($stats['total_this_month'] ?? 0) }}</div>
            <div style="font-size:.78rem;color:var(--muted);">This Month</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#4f46e5;">?{{ number_format($stats['total_this_year'] ?? 0) }}</div>
            <div style="font-size:.78rem;color:var(--muted);">This Year</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card text-center p-3">
            <div style="font-size:1.5rem;font-weight:800;color:#d97706;">{{ $stats['count'] ?? 0 }}</div>
            <div style="font-size:.78rem;color:var(--muted);">Total Records</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search title, reference..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-6 col-md-2">
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-6 col-md-2">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">All Categories</option>
                    @foreach($categories ?? [] as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fa-solid fa-filter me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span style="font-weight:600;"><i class="fa-solid fa-list me-2" style="color:#059669;"></i>Income Records</span>
        <span class="badge" style="background:rgba(5,150,105,.1);color:#059669;">{{ $incomes->total() ?? 0 }}</span>
    </div>
    <div class="card-body p-0">
        @if(isset($incomes) && $incomes->count())
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Reference</th>
                        <th>Amount</th>
                        <th>Mode</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incomes as $income)
                    <tr>
                        <td style="font-size:.82rem;">{{ $income->income_date?->format('d M Y') }}</td>
                        <td>
                            <div style="font-weight:500;font-size:.875rem;">{{ $income->title }}</div>
                            @if($income->description)
                            <div style="font-size:.72rem;color:var(--muted);">{{ Str::limit($income->description, 50) }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge" style="background:rgba(5,150,105,.1);color:#059669;font-size:.72rem;">
                                {{ $income->incomeCategory?->name ?? '—' }}
                            </span>
                        </td>
                        <td style="font-size:.82rem;font-family:monospace;color:var(--muted);">{{ $income->reference_number ?? '—' }}</td>
                        <td style="font-weight:700;color:#059669;">?{{ number_format($income->amount, 2) }}</td>
                        <td style="font-size:.82rem;">{{ ucfirst($income->payment_mode ?? '—') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.incomes.edit', $income) }}" class="btn btn-sm btn-outline-secondary" style="padding:.2rem .5rem;"><i class="fa-solid fa-pen" style="font-size:.72rem;"></i></a>
                                <form method="POST" action="{{ route('admin.incomes.destroy', $income) }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="padding:.2rem .5rem;" onclick="return confirm('Delete this income record?')"><i class="fa-solid fa-trash" style="font-size:.72rem;"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($incomes->hasPages())
        <div class="d-flex justify-content-center py-3">{{ $incomes->withQueryString()->links('pagination::bootstrap-5') }}</div>
        @endif
        @else
        <div class="text-center py-5" style="color:var(--muted);">
            <i class="fa-solid fa-arrow-trend-up d-block mb-2" style="font-size:2.5rem;opacity:.3;"></i>
            <div>No income records found.</div>
            <a href="{{ route('admin.incomes.create') }}" class="btn btn-primary btn-sm mt-3"><i class="fa-solid fa-plus me-1"></i> Add First Income</a>
        </div>
        @endif
    </div>
</div>

@endsection

