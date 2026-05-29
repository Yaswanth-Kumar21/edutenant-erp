@extends('layouts.app')
@section('title', 'Annual Report')

@section('breadcrumb')
    <li class="breadcrumb-item active">Annual Report</li>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-calendar me-2" style="color:#7c3aed;"></i>Annual Report</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Year-wise fee collection summary</p>
    </div>
</div>

{{-- Year Picker --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" class="d-flex align-items-end gap-3 flex-wrap">
            <div>
                <label class="form-label mb-1" style="font-size:.8rem;">Select Year</label>
                <select name="year" class="form-select form-select-sm" onchange="this.form.submit()" style="width:auto;">
                    @foreach(range(now()->year, now()->year - 4) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

{{-- Annual Summary --}}
@php
    $totalAnnual = $annualFees->sum('total');
    $monthNames  = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $maxMonth    = $annualFees->sortByDesc('total')->first();
@endphp

<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card purple">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Total Annual Collection</div>
                    <div class="stat-value" style="font-size:1.5rem;">?{{ number_format($totalAnnual) }}</div>
                    <div class="mt-2" style="font-size:.78rem;opacity:.8;">{{ $year }}</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-chart-line"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card green">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Months with Data</div>
                    <div class="stat-value">{{ $annualFees->count() }}</div>
                    <div class="mt-2" style="font-size:.78rem;opacity:.8;">Out of 12 months</div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card orange">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="stat-label mb-2">Best Month</div>
                    <div class="stat-value" style="font-size:1.3rem;">
                        {{ $maxMonth ? $monthNames[$maxMonth->month - 1] : '—' }}
                    </div>
                    <div class="mt-2" style="font-size:.78rem;opacity:.8;">
                        {{ $maxMonth ? '?'.number_format($maxMonth->total) : 'No data' }}
                    </div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-trophy"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Bar Chart --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-chart-bar me-2" style="color:#7c3aed;"></i>Monthly Fee Collection — {{ $year }}
            </div>
            <div class="card-body p-3">
                @if($annualFees->isEmpty())
                <div class="empty-state py-4">
                    <i class="fa-solid fa-chart-bar d-block mb-2" style="font-size:2rem;opacity:.3;"></i>
                    <p class="mb-0 small">No fee data for {{ $year }}</p>
                </div>
                @else
                <canvas id="annualChart" height="100"></canvas>
                @endif
            </div>
        </div>
    </div>

    {{-- Monthly Breakdown Table --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-table me-2" style="color:#7c3aed;"></i>Monthly Breakdown
            </div>
            <div class="card-body p-0">
                @if($annualFees->isEmpty())
                <div class="text-center py-4" style="color:var(--muted);font-size:.85rem;">No data for {{ $year }}</div>
                @else
                @php $feeByMonth = $annualFees->keyBy('month'); @endphp
                @foreach(range(1, 12) as $m)
                @php $mData = $feeByMonth->get($m); @endphp
                <div class="d-flex align-items-center justify-content-between px-3 py-2"
                     style="border-bottom:1px solid var(--border);font-size:.82rem;">
                    <span style="font-weight:500;width:40px;">{{ $monthNames[$m-1] }}</span>
                    <div class="flex-1 mx-3">
                        @if($mData && $totalAnnual > 0)
                        <div style="height:5px;background:var(--border);border-radius:3px;overflow:hidden;">
                            <div style="height:100%;width:{{ round(($mData->total/$totalAnnual)*100) }}%;background:linear-gradient(90deg,#7c3aed,#a855f7);border-radius:3px;"></div>
                        </div>
                        @else
                        <div style="height:5px;background:var(--border);border-radius:3px;"></div>
                        @endif
                    </div>
                    <span style="font-weight:700;color:{{ $mData ? '#7c3aed' : 'var(--muted)' }};min-width:80px;text-align:right;">
                        {{ $mData ? '?'.number_format($mData->total) : '—' }}
                    </span>
                </div>
                @endforeach
                <div class="d-flex align-items-center justify-content-between px-3 py-2"
                     style="background:var(--bg);font-size:.875rem;font-weight:700;">
                    <span>Total</span>
                    <span style="color:#7c3aed;">?{{ number_format($totalAnnual) }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function(){
    @if(!$annualFees->isEmpty())
    const ctx = document.getElementById('annualChart');
    if(ctx){
        const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        const data   = @json($annualFees);
        const labels = months;
        const values = months.map((_, i) => {
            const d = data.find(x => x.month === i+1);
            return d ? parseFloat(d.total) : 0;
        });
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Fee Collection (?)',
                    data: values,
                    backgroundColor: values.map(v => v > 0 ? 'rgba(124,58,237,0.7)' : 'rgba(124,58,237,0.15)'),
                    borderColor: '#7c3aed',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend:{display:false}, tooltip:{callbacks:{label:c=>'?'+c.parsed.y.toLocaleString('en-IN')}} },
                scales: {
                    y: { beginAtZero:true, grid:{color:'rgba(0,0,0,0.04)'}, ticks:{callback:v=>'?'+(v>=1000?(v/1000).toFixed(0)+'K':v),font:{size:11}} },
                    x: { grid:{display:false}, ticks:{font:{size:11}} }
                }
            }
        });
    }
    @endif
})();
</script>
@endpush

