<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Message;
use App\Models\Stream;
use App\Services\TenantService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::where('tenant_id', TenantService::getTenantId())
            ->with(['sentBy', 'branch'])
            ->latest()
            ->paginate(20);
        return view('admin.messages.index', compact('messages'));
    }

    public function create()
    {
        $tenantId = TenantService::getTenantId();
        $streams  = Stream::where('tenant_id', $tenantId)->get();
        $branches = Branch::where('tenant_id', $tenantId)->with('course')->get();
        return view('admin.messages.create', compact('streams', 'branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject'        => 'nullable|string|max:200',
            'body'           => 'required|string',
            'channel'        => 'required|in:sms,email,whatsapp,all',
            'recipient_type' => 'required|in:all,stream,branch,individual',
        ]);
        Message::create(array_merge($data, [
            'tenant_id' => TenantService::getTenantId(),
            'sent_by'   => auth()->id(),
            'status'    => 'sent',
            'sent_at'   => now(),
        ]));
        return redirect()->route('admin.messages.index')->with('success', 'Message sent.');
    }

    public function show(Message $message) { return view('admin.messages.show', compact('message')); }
    public function edit(Message $message) { return view('admin.messages.edit', compact('message')); }
    public function update(Request $request, Message $message) { return redirect()->route('admin.messages.index'); }
    public function destroy(Message $message) { $message->delete(); return redirect()->route('admin.messages.index'); }
}
