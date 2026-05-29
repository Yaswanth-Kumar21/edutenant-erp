<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\OnboardingService;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $query = Tenant::withCount(['students', 'staff'])->latest();

        if (request()->filled('search')) {
            $s = request('search');
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('slug', 'like', "%{$s}%")
                ->orWhere('email', 'like', "%{$s}%"));
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        $tenants = $query->paginate(15)->withQueryString();

        // Attach onboarding badge to each tenant
        $onboardingBadges = [];
        foreach ($tenants as $tenant) {
            $onboardingBadges[$tenant->id] = OnboardingService::getBadge($tenant);
        }

        return view('super-admin.tenants.index', compact('tenants', 'onboardingBadges'));
    }

    public function create()
    {
        return view('super-admin.tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:200',
            'slug'                 => 'required|string|max:100|unique:tenants',
            'email'                => 'required|email|unique:tenants',
            'phone'                => 'nullable|string|max:20',
            'principal_name'       => 'nullable|string|max:150',
            'affiliation_number'   => 'nullable|string|max:100',
            'website'              => 'nullable|url|max:200',
            'domain'               => 'nullable|string|max:200',
            'address'              => 'nullable|string|max:500',
            'city'                 => 'nullable|string|max:100',
            'state'                => 'nullable|string|max:100',
            'pincode'              => 'nullable|string|max:10',
        ]);

        $validated['status'] = 'active';

        $tenant = Tenant::create($validated);

        return redirect()
            ->route('super.tenants.show', $tenant)
            ->with('success', "Institution '{$tenant->name}' created successfully. Complete the onboarding steps below.");
    }

    public function show(Tenant $tenant)
    {
        $tenant->loadCount(['students', 'staff']);

        // Get real-time onboarding status
        $onboarding = OnboardingService::getStatus($tenant);

        return view('super-admin.tenants.show', compact('tenant', 'onboarding'));
    }

    public function edit(Tenant $tenant)
    {
        return view('super-admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:200',
            'status'               => 'required|in:active,inactive,suspended',
            'email'                => 'nullable|email|max:150',
            'phone'                => 'nullable|string|max:20',
            'principal_name'       => 'nullable|string|max:150',
            'affiliation_number'   => 'nullable|string|max:100',
            'website'              => 'nullable|url|max:200',
            'domain'               => 'nullable|string|max:200',
            'address'              => 'nullable|string|max:500',
            'city'                 => 'nullable|string|max:100',
            'state'                => 'nullable|string|max:100',
            'pincode'              => 'nullable|string|max:10',
        ]);

        $tenant->update($validated);

        return redirect()
            ->route('super.tenants.show', $tenant)
            ->with('success', 'Institution updated successfully.');
    }

    public function destroy(Tenant $tenant)
    {
        $name = $tenant->name;
        $tenant->delete();
        return redirect()->route('super.tenants.index')->with('success', "'{$name}' has been deleted.");
    }

    public function switchTenant(Request $request, Tenant $tenant)
    {
        session(['tenant_id' => $tenant->id]);
        return redirect()->route('dashboard')->with('info', "Switched to {$tenant->name}");
    }
}
