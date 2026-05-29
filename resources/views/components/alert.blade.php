@props([
    'type'    => 'info',
    'message' => '',
])

@php
    $config = [
        'success' => [
            'bg'     => '#f0fdf4',
            'border' => '#bbf7d0',
            'color'  => '#166534',
            'icon'   => 'fa-circle-check',
            'title'  => 'Success',
        ],
        'error' => [
            'bg'     => '#fef2f2',
            'border' => '#fecaca',
            'color'  => '#991b1b',
            'icon'   => 'fa-circle-xmark',
            'title'  => 'Error',
        ],
        'warning' => [
            'bg'     => '#fffbeb',
            'border' => '#fde68a',
            'color'  => '#92400e',
            'icon'   => 'fa-triangle-exclamation',
            'title'  => 'Warning',
        ],
        'info' => [
            'bg'     => '#eff6ff',
            'border' => '#bfdbfe',
            'color'  => '#1e40af',
            'icon'   => 'fa-circle-info',
            'title'  => 'Info',
        ],
    ];

    $c = $config[$type] ?? $config['info'];
@endphp

<div class="alert d-flex align-items-start gap-3 mb-3"
     role="alert"
     style="background:{{ $c['bg'] }};
            border:1px solid {{ $c['border'] }};
            border-radius:0.75rem;
            color:{{ $c['color'] }};
            padding:0.875rem 1rem;">

    <i class="fa-solid {{ $c['icon'] }} mt-1 flex-shrink-0" style="font-size:1rem;"></i>

    <div class="flex-1">
        <strong style="font-size:0.875rem;">{{ $c['title'] }}</strong>
        <div style="font-size:0.85rem;margin-top:2px;">
            {{ $message }}
            {{ $slot }}
        </div>
    </div>

    <button type="button"
            class="btn-close ms-auto flex-shrink-0"
            data-bs-dismiss="alert"
            aria-label="Close"
            style="font-size:0.7rem;opacity:0.6;filter:none;
                   background:none;border:none;cursor:pointer;padding:0.25rem;">
        <i class="fa-solid fa-xmark" style="color:{{ $c['color'] }};font-size:0.9rem;"></i>
    </button>
</div>
