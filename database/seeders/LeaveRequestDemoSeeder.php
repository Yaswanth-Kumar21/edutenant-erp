<?php

namespace Database\Seeders;

use App\Models\LeaveRequest;
use App\Models\Staff;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LeaveRequestDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'svc')->firstOrFail();
        $admin  = User::where('email', 'admin@svc.edu')->firstOrFail();
        $staff  = Staff::where('tenant_id', $tenant->id)->where('status', 'active')->get();

        if ($staff->isEmpty()) {
            $this->command->error('No staff found.');
            return;
        }

        LeaveRequest::where('tenant_id', $tenant->id)->forceDelete();

        $leaveTypes = ['casual', 'sick', 'earned', 'casual', 'sick', 'other'];
        $reasons = [
            'casual'  => 'Personal work — need to attend family function',
            'sick'    => 'Suffering from fever and cold, doctor advised rest',
            'earned'  => 'Annual leave — travelling out of station',
            'other'   => 'Need to attend government office work',
        ];

        $leaves = [
            // Approved leaves (past)
            ['staff_idx' => 0, 'type' => 'sick',   'from' => '-45 days', 'days' => 2, 'status' => 'approved'],
            ['staff_idx' => 1, 'type' => 'casual',  'from' => '-38 days', 'days' => 1, 'status' => 'approved'],
            ['staff_idx' => 2, 'type' => 'earned',  'from' => '-30 days', 'days' => 3, 'status' => 'approved'],
            ['staff_idx' => 3, 'type' => 'sick',   'from' => '-25 days', 'days' => 2, 'status' => 'approved'],
            ['staff_idx' => 4, 'type' => 'casual',  'from' => '-20 days', 'days' => 1, 'status' => 'approved'],
            ['staff_idx' => 0, 'type' => 'casual',  'from' => '-15 days', 'days' => 1, 'status' => 'approved'],
            // Rejected leaves
            ['staff_idx' => 1, 'type' => 'earned',  'from' => '-12 days', 'days' => 5, 'status' => 'rejected'],
            ['staff_idx' => 2, 'type' => 'casual',  'from' => '-10 days', 'days' => 2, 'status' => 'rejected'],
            // Pending leaves (recent — awaiting approval)
            ['staff_idx' => 3, 'type' => 'sick',   'from' => '-3 days',  'days' => 3, 'status' => 'pending'],
            ['staff_idx' => 4, 'type' => 'casual',  'from' => '-2 days',  'days' => 1, 'status' => 'pending'],
            ['staff_idx' => 5, 'type' => 'other',   'from' => '-1 day',   'days' => 1, 'status' => 'pending'],
            // Future leave
            ['staff_idx' => 0, 'type' => 'earned',  'from' => '+5 days',  'days' => 4, 'status' => 'pending'],
        ];

        $created = 0;
        foreach ($leaves as $l) {
            $staffMember = $staff->values()->get($l['staff_idx'] % $staff->count());
            if (!$staffMember) continue;

            $fromDate = Carbon::now()->modify($l['from'])->toDateString();
            $toDate   = Carbon::parse($fromDate)->addDays($l['days'] - 1)->toDateString();

            LeaveRequest::create([
                'tenant_id'         => $tenant->id,
                'staff_id'          => $staffMember->id,
                'leave_type'        => $l['type'],
                'from_date'         => $fromDate,
                'to_date'           => $toDate,
                'total_days'        => $l['days'],
                'reason'            => $reasons[$l['type']] ?? 'Personal reason',
                'status'            => $l['status'],
                'approved_by'       => in_array($l['status'], ['approved', 'rejected']) ? $admin->id : null,
                'approved_at'       => in_array($l['status'], ['approved', 'rejected']) ? Carbon::parse($fromDate)->subDay() : null,
                'rejection_reason'  => $l['status'] === 'rejected' ? 'Leave not approved due to exam season — insufficient staff coverage.' : null,
            ]);
            $created++;
        }

        $pending  = LeaveRequest::where('tenant_id', $tenant->id)->where('status', 'pending')->count();
        $approved = LeaveRequest::where('tenant_id', $tenant->id)->where('status', 'approved')->count();
        $rejected = LeaveRequest::where('tenant_id', $tenant->id)->where('status', 'rejected')->count();

        $this->command->info("✅ Leave Requests Seeded: {$created} total");
        $this->command->info("   Pending  : {$pending}");
        $this->command->info("   Approved : {$approved}");
        $this->command->info("   Rejected : {$rejected}");
    }
}
