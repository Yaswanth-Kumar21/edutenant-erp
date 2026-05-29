<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\StaffRole;
use App\Services\StaffService;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    // ─── Listing ──────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $tenantId = TenantService::getTenantId();

        $query = Staff::where('tenant_id', $tenantId)->with('staffRole');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('staff_code', 'like', "%{$s}%")
                  ->orWhere('designation', 'like', "%{$s}%")
            );
        }

        if ($request->filled('staff_type')) $query->where('staff_type', $request->staff_type);
        if ($request->filled('status'))     $query->where('status', $request->status);
        if ($request->filled('department')) $query->where('department', $request->department);

        $staff      = $query->latest()->paginate(20)->withQueryString();
        $stats      = StaffService::getStats($tenantId);
        $staffRoles = StaffRole::where('tenant_id', $tenantId)->where('is_active', true)->get();

        return view('admin.staff.index', compact('staff', 'stats', 'staffRoles'));
    }

    // ─── Create ───────────────────────────────────────────────────────────────

    public function create()
    {
        $tenantId   = TenantService::getTenantId();
        $staffRoles = StaffRole::where('tenant_id', $tenantId)->where('is_active', true)->get();

        return view('admin.staff.create', compact('staffRoles'));
    }

    public function store(Request $request)
    {
        $tenantId = TenantService::getTenantId();

        $data = $request->validate([
            'name'                     => 'required|string|max:150',
            'staff_type'               => 'required|in:teaching,non_teaching',
            'staff_role_id'            => 'nullable|exists:staff_roles,id',
            'designation'              => 'nullable|string|max:100',
            'department'               => 'nullable|string|max:100',
            'qualification'            => 'nullable|string|max:150',
            'subject'                  => 'nullable|string|max:100',
            'email'                    => 'nullable|email|max:150',
            'phone'                    => 'nullable|string|max:20',
            'gender'                   => 'nullable|in:male,female,other',
            'date_of_birth'            => 'nullable|date',
            'date_of_joining'          => 'nullable|date',
            'address'                  => 'nullable|string|max:500',
            'aadhaar_number'           => 'nullable|string|max:20',
            'pan_number'               => 'nullable|string|max:20',
            'bank_account'             => 'nullable|string|max:30',
            'bank_name'                => 'nullable|string|max:100',
            'ifsc_code'                => 'nullable|string|max:20',
            'monthly_salary'           => 'required|numeric|min:0',
            'basic_salary'             => 'nullable|numeric|min:0',
            'hra'                      => 'nullable|numeric|min:0',
            'da'                       => 'nullable|numeric|min:0',
            'other_allowances'         => 'nullable|numeric|min:0',
            'pf_deduction'             => 'nullable|numeric|min:0',
            'tax_deduction'            => 'nullable|numeric|min:0',
            'allowed_holidays_per_month' => 'nullable|integer|min:0|max:30',
            'photo'                    => 'nullable|image|max:2048',
        ]);

        // Photo upload
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store("staff/{$tenantId}", 'public');
        }

        $data['tenant_id']  = $tenantId;
        $data['staff_code'] = StaffService::generateStaffCode($tenantId);
        $data['allowed_holidays_per_month'] = $data['allowed_holidays_per_month'] ?? 2;
        $data['salary_calculation_days']    = 30;

        Staff::create($data);

        return redirect()->route('admin.staff.index')
            ->with('success', 'Staff member added successfully.');
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show(Staff $staff)
    {
        $this->authorizeTenant($staff);

        $staff->load(['staffRole', 'leaveRequests' => fn($q) => $q->latest()->limit(5)]);

        $tenantId = TenantService::getTenantId();

        // Recent attendance (last 30 days)
        $recentAttendance = StaffAttendance::where('staff_id', $staff->id)
            ->where('attendance_date', '>=', now()->subDays(30))
            ->orderByDesc('attendance_date')
            ->get();

        // Current month salary calculation
        $salaryCalc = StaffService::calculateNetSalary($staff, now()->month, now()->year);

        // Recent payrolls
        $recentPayrolls = Payroll::where('staff_id', $staff->id)
            ->orderByDesc('year')->orderByDesc('month')
            ->limit(6)->get();

        return view('admin.staff.show', compact(
            'staff', 'recentAttendance', 'salaryCalc', 'recentPayrolls'
        ));
    }

    // ─── Edit / Update ────────────────────────────────────────────────────────

    public function edit(Staff $staff)
    {
        $this->authorizeTenant($staff);
        $tenantId   = TenantService::getTenantId();
        $staffRoles = StaffRole::where('tenant_id', $tenantId)->where('is_active', true)->get();

        return view('admin.staff.edit', compact('staff', 'staffRoles'));
    }

    public function update(Request $request, Staff $staff)
    {
        $this->authorizeTenant($staff);

        $data = $request->validate([
            'name'                     => 'required|string|max:150',
            'staff_type'               => 'required|in:teaching,non_teaching',
            'staff_role_id'            => 'nullable|exists:staff_roles,id',
            'designation'              => 'nullable|string|max:100',
            'department'               => 'nullable|string|max:100',
            'qualification'            => 'nullable|string|max:150',
            'subject'                  => 'nullable|string|max:100',
            'email'                    => 'nullable|email|max:150',
            'phone'                    => 'nullable|string|max:20',
            'gender'                   => 'nullable|in:male,female,other',
            'date_of_birth'            => 'nullable|date',
            'date_of_joining'          => 'nullable|date',
            'address'                  => 'nullable|string|max:500',
            'aadhaar_number'           => 'nullable|string|max:20',
            'pan_number'               => 'nullable|string|max:20',
            'bank_account'             => 'nullable|string|max:30',
            'bank_name'                => 'nullable|string|max:100',
            'ifsc_code'                => 'nullable|string|max:20',
            'monthly_salary'           => 'required|numeric|min:0',
            'basic_salary'             => 'nullable|numeric|min:0',
            'hra'                      => 'nullable|numeric|min:0',
            'da'                       => 'nullable|numeric|min:0',
            'other_allowances'         => 'nullable|numeric|min:0',
            'pf_deduction'             => 'nullable|numeric|min:0',
            'tax_deduction'            => 'nullable|numeric|min:0',
            'allowed_holidays_per_month' => 'nullable|integer|min:0|max:30',
            'status'                   => 'required|in:active,inactive,resigned',
            'photo'                    => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($staff->photo) Storage::disk('public')->delete($staff->photo);
            $data['photo'] = $request->file('photo')->store(
                "staff/" . TenantService::getTenantId(), 'public'
            );
        } else {
            unset($data['photo']);
        }

        $staff->update($data);

        return redirect()->route('admin.staff.show', $staff)
            ->with('success', 'Staff profile updated successfully.');
    }

    // ─── Delete ───────────────────────────────────────────────────────────────

    public function destroy(Staff $staff)
    {
        $this->authorizeTenant($staff);
        $staff->delete();

        return redirect()->route('admin.staff.index')
            ->with('success', "{$staff->name} has been removed.");
    }

    // ─── Leave Management ─────────────────────────────────────────────────────

    public function leaves(Request $request)
    {
        $tenantId = TenantService::getTenantId();

        $query = LeaveRequest::where('tenant_id', $tenantId)->with('staff');

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('staff_id')) $query->where('staff_id', $request->staff_id);

        $leaves    = $query->latest()->paginate(20)->withQueryString();
        $staffList = Staff::where('tenant_id', $tenantId)->where('status', 'active')->orderBy('name')->get();

        return view('admin.staff.leaves', compact('leaves', 'staffList'));
    }

    public function storeLeave(Request $request)
    {
        $tenantId = TenantService::getTenantId();

        $data = $request->validate([
            'staff_id'   => 'required|exists:staff,id',
            'leave_type' => 'required|in:casual,sick,earned,maternity,paternity,unpaid,other',
            'from_date'  => 'required|date',
            'to_date'    => 'required|date|after_or_equal:from_date',
            'reason'     => 'required|string|max:500',
        ]);

        $data['tenant_id']  = $tenantId;
        $data['total_days'] = \Carbon\Carbon::parse($data['from_date'])
            ->diffInDays(\Carbon\Carbon::parse($data['to_date'])) + 1;

        LeaveRequest::create($data);

        return back()->with('success', 'Leave request submitted.');
    }

    public function approveLeave(LeaveRequest $leave)
    {
        $leave->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Leave approved.');
    }

    public function rejectLeave(Request $request, LeaveRequest $leave)
    {
        $request->validate(['rejection_reason' => 'required|string|max:300']);

        $leave->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by'      => auth()->id(),
        ]);

        return back()->with('success', 'Leave rejected.');
    }

    // ─── Staff Roles ──────────────────────────────────────────────────────────

    public function roles()
    {
        $tenantId = TenantService::getTenantId();
        $roles    = StaffRole::where('tenant_id', $tenantId)->withCount('staff')->get();

        return view('admin.staff.roles', compact('roles'));
    }

    public function storeRole(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'department' => 'nullable|string|max:100',
            'staff_type' => 'required|in:teaching,non_teaching,both',
            'description'=> 'nullable|string|max:300',
        ]);

        $data['tenant_id'] = TenantService::getTenantId();
        StaffRole::create($data);

        return back()->with('success', 'Staff role created.');
    }

    // ─── Private ──────────────────────────────────────────────────────────────

    private function authorizeTenant(Staff $staff): void
    {
        if ($staff->tenant_id !== TenantService::getTenantId()) {
            abort(403, 'Unauthorized access.');
        }
    }
}
