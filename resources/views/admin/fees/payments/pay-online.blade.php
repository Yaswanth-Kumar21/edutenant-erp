@extends('layouts.app')

@section('title', 'Online Payment — ' . $payment->receipt_number)

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-credit-card me-2" style="color:#4f46e5;"></i>Online Fee Payment</h1>
        <p style="color:var(--muted);font-size:0.875rem;margin:0;">Secure payment via Razorpay</p>
    </div>
    <a href="{{ route('admin.fees.payments.show', $payment) }}" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-receipt me-2" style="color:#059669;"></i>Payment Summary
            </div>
            <div class="card-body">
                {{-- Student Info --}}
                <div class="d-flex align-items-center gap-3 mb-4 pb-3" style="border-bottom:1px solid var(--border);">
                    <img src="{{ $payment->student?->photo_url }}" alt=""
                         class="rounded-circle" style="width:48px;height:48px;object-fit:cover;">
                    <div>
                        <div style="font-weight:700;">{{ $payment->student?->full_name }}</div>
                        <div style="font-size:0.78rem;color:var(--muted);">
                            {{ $payment->student?->admission_number }} &bull; {{ $payment->student?->branch?->name }}
                        </div>
                    </div>
                </div>

                {{-- Fee Details --}}
                <div class="row g-2 mb-4" style="font-size:0.875rem;">
                    <div class="col-6">
                        <div style="color:var(--muted);font-size:0.75rem;">Fee Type</div>
                        <div style="font-weight:600;">{{ $payment->feeType?->name }}</div>
                    </div>
                    <div class="col-6">
                        <div style="color:var(--muted);font-size:0.75rem;">Academic Year</div>
                        <div style="font-weight:600;">{{ $payment->academicYear?->name }}</div>
                    </div>
                    <div class="col-6">
                        <div style="color:var(--muted);font-size:0.75rem;">Receipt No</div>
                        <div style="font-weight:600;font-family:monospace;color:#4f46e5;">{{ $payment->receipt_number }}</div>
                    </div>
                    <div class="col-6">
                        <div style="color:var(--muted);font-size:0.75rem;">Status</div>
                        <span class="badge" style="background:#fef3c7;color:#92400e;">{{ ucfirst($payment->status) }}</span>
                    </div>
                </div>

                {{-- Amount --}}
                <div class="p-3 rounded mb-4 text-center"
                     style="background:linear-gradient(135deg,rgba(79,70,229,0.08),rgba(124,58,237,0.08));border:2px solid rgba(79,70,229,0.2);">
                    <div style="font-size:0.75rem;color:var(--muted);margin-bottom:4px;">Amount to Pay</div>
                    <div style="font-size:2rem;font-weight:800;color:#4f46e5;">₹{{ number_format($payment->balance, 2) }}</div>
                </div>

                @if(config('services.razorpay.key_id') && config('services.razorpay.key_id') !== 'rzp_test_demo')
                {{-- Real Razorpay Button --}}
                <button id="pay-btn" class="btn btn-primary w-100 py-3" style="font-size:1rem;font-weight:600;">
                    <i class="fa-solid fa-lock me-2"></i> Pay Securely with Razorpay
                </button>
                <div class="text-center mt-2" style="font-size:0.72rem;color:var(--muted);">
                    <i class="fa-solid fa-shield-halved me-1"></i>
                    256-bit SSL encrypted. Your payment is secure.
                </div>
                @else
                {{-- Demo Mode --}}
                <div class="alert alert-info" style="border-radius:0.75rem;">
                    <i class="fa-solid fa-circle-info me-2"></i>
                    <strong>Demo Mode:</strong> Razorpay is not configured. Add <code>RAZORPAY_KEY_ID</code> and <code>RAZORPAY_KEY_SECRET</code> to your <code>.env</code> file to enable live payments.
                </div>
                <a href="{{ route('admin.fees.payments.show', $payment) }}" class="btn btn-outline-secondary w-100">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Payment
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
@if(config('services.razorpay.key_id') && config('services.razorpay.key_id') !== 'rzp_test_demo')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('pay-btn')?.addEventListener('click', async function () {
    this.disabled = true;
    this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i> Initializing...';

    try {
        const res = await fetch('{{ route("admin.payments.create-order", $payment) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });

        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'Failed to create order');

        const options = {
            key:         data.key_id,
            amount:      data.amount,
            currency:    data.currency,
            name:        data.name,
            description: data.description,
            order_id:    data.order_id,
            prefill:     data.prefill,
            theme:       { color: '#4f46e5' },
            handler: function (response) {
                // Submit verification form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.payments.verify", $payment) }}';

                const fields = {
                    '_token':               '{{ csrf_token() }}',
                    'razorpay_order_id':    response.razorpay_order_id,
                    'razorpay_payment_id':  response.razorpay_payment_id,
                    'razorpay_signature':   response.razorpay_signature,
                };

                Object.entries(fields).forEach(([k, v]) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = k;
                    input.value = v;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            },
            modal: {
                ondismiss: () => {
                    document.getElementById('pay-btn').disabled = false;
                    document.getElementById('pay-btn').innerHTML = '<i class="fa-solid fa-lock me-2"></i> Pay Securely with Razorpay';
                }
            }
        };

        const rzp = new Razorpay(options);
        rzp.open();

    } catch (err) {
        Swal.fire({ icon: 'error', title: 'Error', text: err.message });
        this.disabled = false;
        this.innerHTML = '<i class="fa-solid fa-lock me-2"></i> Pay Securely with Razorpay';
    }
});
</script>
@endif
@endpush

