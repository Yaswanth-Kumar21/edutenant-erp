<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeType;
use App\Services\TenantService;
use Illuminate\Http\Request;

class FeeTypeController extends Controller
{
    public function index()
    {
        $feeTypes = FeeType::where('tenant_id', TenantService::getTenantId())->orderBy('sort_order')->get();
        return view('admin.fees.types.index', compact('feeTypes'));
    }

    public function create() { return view('admin.fees.types.create'); }

    public function store(Request $request)
    {
        $tenantId = TenantService::getTenantId();

        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'code'      => [
                'required', 'string', 'max:30',
                \Illuminate\Validation\Rule::unique('fee_types')->where('tenant_id', $tenantId),
            ],
            'frequency' => 'required|in:one_time,per_semester,per_year,monthly',
            'amount'    => 'required|numeric|min:0',
        ]);
        FeeType::create(array_merge($data, ['tenant_id' => $tenantId]));
        return redirect()->route('admin.fees.types.index')->with('success', 'Fee type created.');
    }

    public function show(FeeType $type) { return view('admin.fees.types.show', compact('type')); }
    public function edit(FeeType $type) { return view('admin.fees.types.edit', compact('type')); }

    public function update(Request $request, FeeType $type)
    {
        $type->update($request->validate(['name' => 'required|string|max:100', 'amount' => 'required|numeric|min:0']));
        return redirect()->route('admin.fees.types.index')->with('success', 'Fee type updated.');
    }

    public function destroy(FeeType $type)
    {
        $type->delete();
        return redirect()->route('admin.fees.types.index')->with('success', 'Fee type deleted.');
    }
}
