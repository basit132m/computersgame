<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedIp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IpBlockController extends Controller
{
    public function index()
    {
        $ips = BlockedIp::orderByDesc('blocked_at')->paginate(30);
        return view('admin.ip-blocks.index', compact('ips'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ip_address' => 'required|ip|unique:blocked_ips,ip_address',
            'reason'     => 'nullable|string|max:255',
        ], ['ip_address.required' => 'عنوان IP مطلوب', 'ip_address.ip' => 'عنوان IP غير صحيح']);

        BlockedIp::create($request->only(['ip_address', 'reason']));
        Cache::forget('blocked_ip_' . $request->ip_address);

        return back()->with('success', 'تم حظر عنوان IP');
    }

    public function destroy(BlockedIp $ip)
    {
        Cache::forget('blocked_ip_' . $ip->ip_address);
        $ip->delete();
        return back()->with('success', 'تم رفع الحظر');
    }
}
