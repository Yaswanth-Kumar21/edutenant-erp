@extends('layouts.super-admin-app')
@section('title', 'Edit — ' . $tenant->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('super.tenants.index') }}" style="color:var(--blue);">Institutions</a></li>
    <li class="breadcrumb-item"><a href="{{ route('super.tenants.show', $tenant) }}" style="color:var(--blue);">{{ Str::limit($tenant->name,25) }}</a></li>
    <li class="breadcrumb-item active" style="color:var(--muted);">Edit</li>
@endsection
@section('content')

<div class="page-header d-flex align-items-start justify-content-between flex-wrap gap-3">
    <div>
        <h1 class="page-title">Edit Institution</h1>
        <p class="page-sub">{{ $tenant->name }}</p>
    </div>
    <a href="{{ route('super.tenants.show', $tenant) }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
</div>

@if($errors->any())
<div class="alert alert-danger mb-4">
    <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
    <div><strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3" style="font-size:12px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <form method="POST" action="{{ route('super.tenants.update', $tenant) }}">
            @csrf @method('PUT')
            <div class="card mb-4">
                <div class="card-header"><span style="font-weight:700;">Basic Information</span></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Institution Name <span style="color:var(--red);">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $tenant->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status <span style="color:var(--red);">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active"    {{ old('status',$tenant->status)==='active'    ? 'selected':'' }}>Active</option>
                                <option value="inactive"  {{ old('status',$tenant->status)==='inactive'  ? 'selected':'' }}>Inactive</option>
                                <option value="suspended" {{ old('status',$tenant->status)==='suspended' ? 'selected':'' }}>Suspended</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email',$tenant->email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone',$tenant->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Principal Name</label>
                            <input type="text" name="principal_name" class="form-control" value="{{ old('principal_name',$tenant->principal_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Affiliation Number</label>
                            <input type="text" name="affiliation_number" class="form-control" value="{{ old('affiliation_number',$tenant->affiliation_number) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Website</label>
                            <input type="url" name="website" class="form-control" value="{{ old('website',$tenant->website) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Custom Domain</label>
                            <input type="text" name="domain" class="form-control" value="{{ old('domain',$tenant->domain) }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header"><span style="font-weight:700;">Address</span></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Street Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address',$tenant->address) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city',$tenant->city) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control" value="{{ old('state',$tenant->state) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pincode</label>
                            <input type="text" name="pincode" class="form-control" value="{{ old('pincode',$tenant->pincode) }}">
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn-primary"><i class="fa-solid fa-save"></i> Save Changes</button>
                <a href="{{ route('super.tenants.show', $tenant) }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><span style="font-weight:700;font-size:13px;">Institution Info</span></div>
            <div class="card-body p-0">
                @php $meta = [
                    ['label'=>'Slug',     'value'=>$tenant->slug, 'mono'=>true],
                    ['label'=>'Students', 'value'=>$tenant->students_count ?? 0],
                    ['label'=>'Staff',    'value'=>$tenant->staff_count ?? 0],
                    ['label'=>'Created',  'value'=>$tenant->created_at?->format('d M Y')],
                ]; @endphp
                @foreach($meta as $i => $m)
                <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:{{ $i < count($meta)-1 ? '1px solid var(--border)' : 'none' }};">
                    <span style="font-size:12px;color:var(--muted);">{{ $m['label'] }}</span>
                    <span style="font-size:12px;font-weight:600;color:var(--text);{{ isset($m['mono']) ? 'font-family:monospace;' : '' }}">{{ $m['value'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
