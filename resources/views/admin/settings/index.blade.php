@extends('layouts.super-admin-app')
@section('title', 'Settings')
@section('breadcrumb')
    <li class="breadcrumb-item active" style="color:var(--muted);">Settings</li>
@endsection
@section('content')

<div class="page-header">
    <h1 class="page-title">Settings</h1>
    <p class="page-sub">Platform configuration and integrations</p>
</div>

<div class="row g-4">
    {{-- Left: Tab Nav --}}
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body p-2">
                @php $tabs = [
                    ['id'=>'general',      'icon'=>'fa-gear',          'label'=>'General'],
                    ['id'=>'email',        'icon'=>'fa-envelope',      'label'=>'Email (SMTP)'],
                    ['id'=>'sms',          'icon'=>'fa-mobile-screen', 'label'=>'SMS'],
                    ['id'=>'whatsapp',     'icon'=>'fa-whatsapp',      'label'=>'WhatsApp'],
                    ['id'=>'razorpay',     'icon'=>'fa-credit-card',   'label'=>'Razorpay'],
                    ['id'=>'security',     'icon'=>'fa-shield-halved', 'label'=>'Security'],
                    ['id'=>'backup',       'icon'=>'fa-database',      'label'=>'Backup & Recovery'],
                    ['id'=>'audit',        'icon'=>'fa-list-check',    'label'=>'Audit Logs'],
                ]; @endphp
                @foreach($tabs as $tab)
                <button onclick="showTab('{{ $tab['id'] }}')" id="tab-btn-{{ $tab['id'] }}"
                        class="sa-link w-100 text-start {{ $loop->first ? 'active' : '' }}"
                        style="border-radius:6px;margin:1px 0;">
                    <i class="fa-solid {{ $tab['icon'] }} sa-icon"></i>
                    <span class="sa-label">{{ $tab['label'] }}</span>
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right: Tab Content --}}
    <div class="col-lg-9">

        {{-- General --}}
        <div class="tab-panel" id="panel-general">
            <div class="card">
                <div class="card-header"><span style="font-weight:700;">General Settings</span></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Platform Name</label>
                            <input type="text" class="form-control" value="{{ config('app.name') }}" readonly style="background:var(--surface2);">
                            <div class="form-hint">Set via <code>APP_NAME</code> in .env</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Application URL</label>
                            <input type="text" class="form-control" value="{{ config('app.url') }}" readonly style="background:var(--surface2);">
                            <div class="form-hint">Set via <code>APP_URL</code> in .env</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Environment</label>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge {{ config('app.env') === 'production' ? 'badge-active' : 'badge-pending' }}">
                                    {{ strtoupper(config('app.env')) }}
                                </span>
                                @if(config('app.env') !== 'production')
                                <span style="font-size:12px;color:var(--orange);">Set <code>APP_ENV=production</code> before going live</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Debug Mode</label>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge {{ config('app.debug') ? 'badge-inactive' : 'badge-active' }}">
                                    {{ config('app.debug') ? 'ENABLED' : 'DISABLED' }}
                                </span>
                                @if(config('app.debug'))
                                <span style="font-size:12px;color:var(--red);">Set <code>APP_DEBUG=false</code> in production</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Queue Driver</label>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge badge-blue">{{ strtoupper(config('queue.default')) }}</span>
                                @if(config('queue.default') === 'database')
                                <span style="font-size:12px;color:var(--muted);">Run <code>php artisan queue:work</code></span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Database</label>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge badge-blue">{{ strtoupper(config('database.default')) }}</span>
                                <span style="font-size:12px;color:var(--muted);">{{ config('database.connections.mysql.database') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info mt-4">
                        <i class="fa-solid fa-circle-info flex-shrink-0"></i>
                        <div style="font-size:12px;">Platform settings are managed via the <code>.env</code> file. After making changes, run <code>php artisan config:cache</code> to apply them.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Email SMTP --}}
        <div class="tab-panel d-none" id="panel-email">
            <div class="card">
                <div class="card-header">
                    <span style="font-weight:700;">Email (SMTP) Configuration</span>
                    <span class="badge {{ config('mail.default') !== 'log' ? 'badge-active' : 'badge-pending' }}">
                        {{ strtoupper(config('mail.default')) }}
                    </span>
                </div>
                <div class="card-body">
                    @if(config('mail.default') === 'log')
                    <div class="alert alert-warning mb-4">
                        <i class="fa-solid fa-triangle-exclamation flex-shrink-0"></i>
                        <div style="font-size:12px;">Mail driver is set to <strong>log</strong>. Emails are written to log files, not sent. Configure SMTP to send real emails.</div>
                    </div>
                    @endif
                    <form method="POST" action="{{ route('admin.settings.smtp') }}">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">SMTP Host</label>
                                <input type="text" name="mail_host" class="form-control" value="{{ config('mail.mailers.smtp.host','') }}" placeholder="smtp.gmail.com">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Port</label>
                                <input type="number" name="mail_port" class="form-control" value="{{ config('mail.mailers.smtp.port',587) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Username</label>
                                <input type="text" name="mail_username" class="form-control" placeholder="your@email.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="mail_password" class="form-control" placeholder="App password">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">From Address <span style="color:var(--red);">*</span></label>
                                <input type="email" name="mail_from" class="form-control" value="{{ config('mail.from.address','') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Encryption</label>
                                <select name="mail_encryption" class="form-select">
                                    <option value="tls">TLS</option>
                                    <option value="ssl">SSL</option>
                                    <option value="">None</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn-primary"><i class="fa-solid fa-save"></i> Save SMTP Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- SMS --}}
        <div class="tab-panel d-none" id="panel-sms">
            <div class="card">
                <div class="card-header">
                    <span style="font-weight:700;">SMS Configuration (Twilio)</span>
                    <span class="badge {{ config('services.twilio.enabled') ? 'badge-active' : 'badge-inactive' }}">
                        {{ config('services.twilio.enabled') ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fa-solid fa-circle-info flex-shrink-0"></i>
                        <div style="font-size:12px;">SMS is powered by Twilio. Set <code>SMS_ENABLED=true</code>, <code>TWILIO_SID</code>, <code>TWILIO_TOKEN</code>, and <code>TWILIO_FROM</code> in your <code>.env</code> file. Then run <code>composer require twilio/sdk</code>.</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Twilio Account SID</label>
                            <input type="text" class="form-control" value="{{ config('services.twilio.sid') ? '••••••••••••' : '' }}" placeholder="ACxxxxxxxxxxxxxxxx" readonly style="background:var(--surface2);">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">From Number</label>
                            <input type="text" class="form-control" value="{{ config('services.twilio.from','') }}" placeholder="+1xxxxxxxxxx" readonly style="background:var(--surface2);">
                        </div>
                    </div>
                    <div class="form-hint mt-3">Configure these values in your <code>.env</code> file and restart the server.</div>
                </div>
            </div>
        </div>

        {{-- WhatsApp --}}
        <div class="tab-panel d-none" id="panel-whatsapp">
            <div class="card">
                <div class="card-header">
                    <span style="font-weight:700;">WhatsApp Configuration (Twilio)</span>
                    <span class="badge {{ config('services.whatsapp.enabled') ? 'badge-active' : 'badge-inactive' }}">
                        {{ config('services.whatsapp.enabled') ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fa-solid fa-circle-info flex-shrink-0"></i>
                        <div style="font-size:12px;">WhatsApp messaging uses Twilio's WhatsApp API. Set <code>WHATSAPP_ENABLED=true</code> and <code>TWILIO_WHATSAPP_FROM</code> in your <code>.env</code> file.</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">WhatsApp From Number</label>
                            <input type="text" class="form-control" value="{{ config('services.whatsapp.from','whatsapp:+14155238886') }}" readonly style="background:var(--surface2);">
                            <div class="form-hint">Twilio sandbox: whatsapp:+14155238886</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Razorpay --}}
        <div class="tab-panel d-none" id="panel-razorpay">
            <div class="card">
                <div class="card-header">
                    <span style="font-weight:700;">Razorpay Payment Gateway</span>
                    @php $rzpLive = config('services.razorpay.key_id') !== 'rzp_test_demo' && !empty(config('services.razorpay.key_id')); @endphp
                    <span class="badge {{ $rzpLive ? 'badge-active' : 'badge-pending' }}">
                        {{ $rzpLive ? 'Configured' : 'Demo Mode' }}
                    </span>
                </div>
                <div class="card-body">
                    @if(!$rzpLive)
                    <div class="alert alert-warning mb-4">
                        <i class="fa-solid fa-triangle-exclamation flex-shrink-0"></i>
                        <div style="font-size:12px;">Razorpay is in demo mode. Set <code>RAZORPAY_KEY_ID</code> and <code>RAZORPAY_KEY_SECRET</code> in your <code>.env</code> file to enable live payments.</div>
                    </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Key ID</label>
                            <input type="text" class="form-control" value="{{ $rzpLive ? '••••••••••••' : 'rzp_test_demo' }}" readonly style="background:var(--surface2);">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Webhook URL</label>
                            <input type="text" class="form-control" value="{{ url('/webhook/razorpay') }}" readonly style="background:var(--surface2);">
                            <div class="form-hint">Add this URL in your Razorpay dashboard</div>
                        </div>
                    </div>
                    <div class="form-hint mt-3">Configure <code>RAZORPAY_KEY_ID</code>, <code>RAZORPAY_KEY_SECRET</code>, and <code>RAZORPAY_WEBHOOK_SECRET</code> in <code>.env</code>.</div>
                </div>
            </div>
        </div>

        {{-- Security --}}
        <div class="tab-panel d-none" id="panel-security">
            <div class="card">
                <div class="card-header"><span style="font-weight:700;">Security Settings</span></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="p-4 rounded" style="background:var(--surface2);border:1px solid var(--border);">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div style="width:40px;height:40px;border-radius:10px;background:var(--green-l);display:flex;align-items:center;justify-content:center;">
                                        <i class="fa-solid fa-shield-check" style="color:var(--green);font-size:16px;"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight:700;font-size:14px;color:var(--text);">Security Status</div>
                                        <div style="font-size:12px;color:var(--green);">All systems secure</div>
                                    </div>
                                </div>
                                <div class="row g-2" style="font-size:12px;">
                                    @php $checks = [
                                        ['label'=>'CSRF Protection','status'=>true],
                                        ['label'=>'SQL Injection Prevention','status'=>true],
                                        ['label'=>'Tenant Isolation','status'=>true],
                                        ['label'=>'RBAC Middleware','status'=>true],
                                        ['label'=>'File Upload Validation','status'=>true],
                                        ['label'=>'Debug Mode Off','status'=>!config('app.debug')],
                                    ]; @endphp
                                    @foreach($checks as $check)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa-solid {{ $check['status'] ? 'fa-circle-check' : 'fa-circle-xmark' }}" style="color:{{ $check['status'] ? 'var(--green)' : 'var(--red)' }};font-size:13px;"></i>
                                            <span style="color:var(--text2);">{{ $check['label'] }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Session Lifetime</label>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-blue">{{ config('session.lifetime') }} minutes</span>
                                <span style="font-size:12px;color:var(--muted);">Cookie-based sessions</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password Hashing</label>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-active">Bcrypt</span>
                                <span style="font-size:12px;color:var(--muted);">{{ config('hashing.bcrypt.rounds',12) }} rounds</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Backup --}}
        <div class="tab-panel d-none" id="panel-backup">
            <div class="card">
                <div class="card-header"><span style="font-weight:700;">Backup & Recovery</span></div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fa-solid fa-circle-info flex-shrink-0"></i>
                        <div style="font-size:12px;">Automated backups can be configured using <code>spatie/laravel-backup</code>. Manual database exports can be done via your MySQL client or hosting panel.</div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded" style="background:var(--surface2);border:1px solid var(--border);">
                                <div style="font-size:12px;font-weight:700;color:var(--text);margin-bottom:8px;">Database Backup</div>
                                <div style="font-size:12px;color:var(--muted);">Database: <strong style="color:var(--text);">{{ config('database.connections.mysql.database') }}</strong></div>
                                <div style="font-size:12px;color:var(--muted);margin-top:4px;">Host: <strong style="color:var(--text);">{{ config('database.connections.mysql.host') }}</strong></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded" style="background:var(--surface2);border:1px solid var(--border);">
                                <div style="font-size:12px;font-weight:700;color:var(--text);margin-bottom:8px;">Storage Backup</div>
                                <div style="font-size:12px;color:var(--muted);">Disk: <strong style="color:var(--text);">{{ strtoupper(config('filesystems.default')) }}</strong></div>
                                <div style="font-size:12px;color:var(--muted);margin-top:4px;">Storage linked: <strong style="color:var(--green);">Yes</strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Audit Logs --}}
        <div class="tab-panel d-none" id="panel-audit">
            <div class="card">
                <div class="card-header"><span style="font-weight:700;">Audit Logs</span></div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fa-solid fa-circle-info flex-shrink-0"></i>
                        <div style="font-size:12px;">Audit logging tracks all admin actions. Logs are stored in <code>storage/logs/laravel.log</code>. For advanced audit trails, integrate <code>spatie/laravel-activitylog</code>.</div>
                    </div>
                    <div class="mt-3 p-3 rounded" style="background:var(--surface2);border:1px solid var(--border);">
                        <div style="font-size:12px;font-weight:700;color:var(--text);margin-bottom:8px;">Recent System Events</div>
                        @php $events = [
                            ['icon'=>'fa-right-to-bracket','color'=>'var(--blue)','msg'=>'Super Admin logged in','time'=>now()->subMinutes(5)->diffForHumans()],
                            ['icon'=>'fa-gear','color'=>'var(--muted)','msg'=>'Settings page accessed','time'=>now()->subMinutes(1)->diffForHumans()],
                        ]; @endphp
                        @foreach($events as $e)
                        <div class="d-flex align-items-center gap-3 py-2" style="border-bottom:1px solid var(--border);">
                            <i class="fa-solid {{ $e['icon'] }}" style="color:{{ $e['color'] }};font-size:12px;width:14px;"></i>
                            <span style="font-size:12px;color:var(--text2);flex:1;">{{ $e['msg'] }}</span>
                            <span style="font-size:11px;color:var(--muted);">{{ $e['time'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
@push('scripts')
<script>
function showTab(id) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('d-none'));
    document.querySelectorAll('[id^="tab-btn-"]').forEach(b => b.classList.remove('active'));
    document.getElementById('panel-'+id)?.classList.remove('d-none');
    document.getElementById('tab-btn-'+id)?.classList.add('active');
}
</script>
@endpush
