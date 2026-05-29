@extends('layouts.app')
@section('title', 'New Message')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.messages.index') }}" style="color:var(--primary);text-decoration:none;">Messages</a></li>
    <li class="breadcrumb-item active">New Message</li>
@endsection
@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fa-solid fa-paper-plane me-2" style="color:#7c3aed;"></i>Send Message</h1>
        <p class="mb-0" style="color:var(--muted);font-size:.875rem;">Send notifications to students, staff, or branches</p>
    </div>
    <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
</div>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="fa-solid fa-envelope me-2" style="color:#7c3aed;"></i>Message Details</div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.messages.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
                               value="{{ old('subject') }}" placeholder="Message subject...">
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Channel <span class="text-danger">*</span></label>
                            <select name="channel" class="form-select @error('channel') is-invalid @enderror" required>
                                <option value="">Select Channel</option>
                                <option value="email" {{ old('channel') === 'email' ? 'selected' : '' }}>Email</option>
                                <option value="sms" {{ old('channel') === 'sms' ? 'selected' : '' }}>SMS</option>
                                <option value="whatsapp" {{ old('channel') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="all" {{ old('channel') === 'all' ? 'selected' : '' }}>All Channels</option>
                            </select>
                            @error('channel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Recipients <span class="text-danger">*</span></label>
                            <select name="recipient_type" class="form-select @error('recipient_type') is-invalid @enderror" required id="recipient-type">
                                <option value="">Select Recipients</option>
                                <option value="all" {{ old('recipient_type') === 'all' ? 'selected' : '' }}>All Students</option>
                                <option value="branch" {{ old('recipient_type') === 'branch' ? 'selected' : '' }}>By Branch</option>
                                <option value="stream" {{ old('recipient_type') === 'stream' ? 'selected' : '' }}>By Stream</option>
                            </select>
                            @error('recipient_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row g-3 mb-3" id="branch-select" style="display:none;">
                        <div class="col-md-6">
                            <label class="form-label">Select Branch</label>
                            <select name="branch_id" class="form-select">
                                <option value="">All Branches</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Select Stream</label>
                            <select name="stream_id" class="form-select">
                                <option value="">All Streams</option>
                                @foreach($streams as $stream)
                                    <option value="{{ $stream->id }}" {{ old('stream_id') == $stream->id ? 'selected' : '' }}>
                                        {{ $stream->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Message Body <span class="text-danger">*</span></label>
                        <textarea name="body" class="form-control @error('body') is-invalid @enderror"
                                  rows="5" placeholder="Type your message here..." required>{{ old('body') }}</textarea>
                        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="p-3 rounded mb-4" style="background:rgba(124,58,237,.06);border:1px solid rgba(124,58,237,.15);font-size:.82rem;color:var(--muted);">
                        <i class="fa-solid fa-circle-info me-1" style="color:#7c3aed;"></i>
                        Messages are queued and sent via the configured channel. Ensure <code>QUEUE_CONNECTION=database</code> and run <code>php artisan queue:work</code>.
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-paper-plane me-2"></i> Send Message
                        </button>
                        <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('recipient-type')?.addEventListener('change', function() {
    const show = ['branch','stream'].includes(this.value);
    document.getElementById('branch-select').style.display = show ? 'flex' : 'none';
});
// Restore on page load
const rt = document.getElementById('recipient-type');
if(rt && ['branch','stream'].includes(rt.value)) {
    document.getElementById('branch-select').style.display = 'flex';
}
</script>
@endpush

