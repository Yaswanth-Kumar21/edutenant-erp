<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stream;
use App\Services\TenantService;
use Illuminate\Http\Request;

class StreamController extends Controller
{
    public function index()
    {
        $streams = Stream::where('tenant_id', TenantService::getTenantId())->get();
        return view('admin.setup.streams.index', compact('streams'));
    }

    public function create()
    {
        return view('admin.setup.streams.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100', 'code' => 'nullable|string|max:20']);
        Stream::create(array_merge($data, ['tenant_id' => TenantService::getTenantId()]));
        return redirect()->route('admin.setup.streams.index')->with('success', 'Stream created.');
    }

    public function show(Stream $stream) { return view('admin.setup.streams.show', compact('stream')); }
    public function edit(Stream $stream) { return view('admin.setup.streams.edit', compact('stream')); }

    public function update(Request $request, Stream $stream)
    {
        $stream->update($request->validate(['name' => 'required|string|max:100', 'code' => 'nullable|string|max:20']));
        return redirect()->route('admin.setup.streams.index')->with('success', 'Stream updated.');
    }

    public function destroy(Stream $stream)
    {
        $stream->delete();
        return redirect()->route('admin.setup.streams.index')->with('success', 'Stream deleted.');
    }
}
