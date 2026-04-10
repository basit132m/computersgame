<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RedirectController extends Controller
{
    public function index()
    {
        $redirects = Redirect::orderByDesc('created_at')->paginate(20);
        return view('admin.redirects.index', compact('redirects'));
    }

    public function create()
    {
        return view('admin.redirects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_url' => 'required|string|max:500',
            'to_url'   => 'required|string|max:500',
            'type'     => 'required|in:301,302',
        ]);

        Redirect::create($request->only(['from_url', 'to_url', 'type', 'active']));
        Cache::forget('all_redirects');

        return redirect()->route('admin.redirects.index')->with('success', 'تم إضافة التحويل');
    }

    public function edit(Redirect $redirect)
    {
        return view('admin.redirects.edit', compact('redirect'));
    }

    public function update(Request $request, Redirect $redirect)
    {
        $redirect->update($request->only(['from_url', 'to_url', 'type', 'active']));
        Cache::forget('all_redirects');
        return redirect()->route('admin.redirects.index')->with('success', 'تم تحديث التحويل');
    }

    public function destroy(Redirect $redirect)
    {
        $redirect->delete();
        Cache::forget('all_redirects');
        return back()->with('success', 'تم حذف التحويل');
    }
}
