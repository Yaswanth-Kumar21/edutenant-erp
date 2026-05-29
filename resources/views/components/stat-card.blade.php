@props([
    'title' => 'Stat',
    'value' => '0',
    'icon'  => 'fa-chart-bar',
    'color' => 'blue',
    'trend' => null,
    'prefix' => '',
    'suffix' => '',
])

@php
    $gradients = [
        'blue'   => 'linear-gradient(135deg, #4f46e5, #7c3aed)',
        'green'  => 'linear-gradient(135deg, #059669, #10b981)',
        'orange' => 'linear-gradient(135deg, #d97706, #f59e0b)',
        'purple' => 'linear-gradient(135deg, #7c3aed, #a855f7)',
        'red'    => 'linear-gradient(135deg, #dc2626, #ef4444)',
        'cyan'   => 'linear-gradient(135deg, #0891b2, #06b6d4)',
        'pink'   => 'linear-gradient(135deg, #db2777, #ec4899)',
    ];

    $gradient = $gradients[$color] ?? $gradients['blue'];

    $trendUp   = $trend && str_starts_with((string)$trend, '+');
    $trendDown = $trend && str_starts_with((string)$trend, '-');
@endphp

<div class="stat-card {{ $color }}"
     style="background:{{ $gradient }};border-radius:0.875rem;padding:1.5rem;
            color:#fff;position:relative;overflow:hidden;
            box-shadow:0 4px 15px rgba(0,0,0,0.15);
            transition:all 0.25s ease;cursor:default;"
     onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,0.2)'"
     onmouseleave="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 15px rgba(0,0,0,0.15)'">

    {{-- Background decoration --}}
    <div style="position:absolute;top:-20px;right:-20px;width:100px;height:100px;
                border-radius:50%;background:rgba(255,255,255,0.08);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-30px;right:20px;width:70px;height:70px;
                border-radius:50%;background:rgba(255,255,255,0.05);pointer-events:none;"></div>

    <div class="d-flex align-items-start justify-content-between position-relative">
        <div>
            <div style="font-size:0.8rem;font-weight:500;opacity:0.85;margin-bottom:0.5rem;
                        text-transform:uppercase;letter-spacing:0.05em;">
                {{ $title }}
            </div>
            <div style="font-size:1.875rem;font-weight:800;line-height:1;margin-bottom:0.5rem;"
                 data-counter
                 data-target="{{ is_numeric(str_replace([',','₹','%'], '', $value)) ? str_replace([',','₹','%'], '', $value) : 0 }}"
                 data-prefix="{{ $prefix }}"
                 data-suffix="{{ $suffix }}">
                {{ $prefix }}{{ $value }}{{ $suffix }}
            </div>
            @if($trend)
                <div style="font-size:0.78rem;opacity:0.9;display:flex;align-items:center;gap:4px;">
                    @if($trendUp)
                        <i class="fa-solid fa-arrow-trend-up"></i>
                    @elseif($trendDown)
                        <i class="fa-solid fa-arrow-trend-down"></i>
                    @else
                        <i class="fa-solid fa-minus"></i>
                    @endif
                    {{ $trend }}
                </div>
            @endif
        </div>
        <div style="font-size:2.25rem;opacity:0.8;flex-shrink:0;">
            <i class="fa-solid {{ $icon }}"></i>
        </div>
    </div>

    {{-- Slot for extra content --}}
    @if($slot->isNotEmpty())
        <div style="margin-top:0.75rem;padding-top:0.75rem;border-top:1px solid rgba(255,255,255,0.15);
                    font-size:0.78rem;opacity:0.85;">
            {{ $slot }}
        </div>
    @endif
</div>
