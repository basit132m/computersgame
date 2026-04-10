<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DownloadLink;
use App\Models\Post;
use Illuminate\Http\Request;

class DownloadLinkController extends Controller
{
    public function index(Request $request)
    {
        $post = Post::findOrFail($request->post_id);
        $links = DownloadLink::where('post_id', $post->id)->orderBy('sort_order')->get();
        return view('admin.download-links.index', compact('post', 'links'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'post_id'   => 'required|exists:posts,id',
            'label'     => 'required|string|max:100',
            'url'       => 'required|url',
            'platform'  => 'nullable|string|max:50',
            'file_size' => 'nullable|string|max:50',
            'version'   => 'nullable|string|max:50',
        ], ['label.required' => 'عنوان الرابط مطلوب', 'url.required' => 'الرابط مطلوب']);

        DownloadLink::create($request->only(['post_id', 'label', 'url', 'platform', 'file_size', 'version']));

        return back()->with('success', 'تم إضافة رابط التحميل');
    }

    public function update(Request $request, DownloadLink $link)
    {
        $request->validate([
            'label' => 'required|string|max:100',
            'url'   => 'required|url',
        ]);

        $link->update($request->only(['label', 'url', 'platform', 'file_size', 'version']));
        return back()->with('success', 'تم تحديث الرابط');
    }

    public function destroy(DownloadLink $link)
    {
        $link->delete();
        return back()->with('success', 'تم حذف الرابط');
    }

    public function reorder(Request $request)
    {
        foreach ($request->order as $index => $id) {
            DownloadLink::where('id', $id)->update(['sort_order' => $index]);
        }
        return response()->json(['success' => true]);
    }
}
