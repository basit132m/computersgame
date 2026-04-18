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
        $query = Post::with(['downloadLinks' => fn($q) => $q->orderBy('sort_order')])
            ->has('downloadLinks')
            ->orderByDesc('updated_at');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $posts = $query->paginate(20)->withQueryString();

        // For add-link form: all posts (for select dropdown)
        $allPosts = Post::orderBy('title')->get(['id', 'title']);

        return view('admin.download-links.index', compact('posts', 'allPosts'));
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
            'label'     => 'required|string|max:100',
            'url'       => 'required|url',
            'platform'  => 'nullable|string|max:50',
            'file_size' => 'nullable|string|max:50',
            'version'   => 'nullable|string|max:50',
        ]);

        $link->update($request->only(['label', 'url', 'platform', 'file_size', 'version']));

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
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
