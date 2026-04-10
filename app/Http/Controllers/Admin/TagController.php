<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::withCount('posts')->orderByDesc('posts_count')->paginate(30);
        return view('admin.tags.index', compact('tags'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100'], ['name.required' => 'اسم الوسم مطلوب']);
        $slug = Str::slug($request->name);
        Tag::firstOrCreate(['slug' => $slug], ['name' => $request->name]);
        return back()->with('success', 'تم إضافة الوسم');
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $tag->update(['name' => $request->name, 'slug' => Str::slug($request->name)]);
        return back()->with('success', 'تم تحديث الوسم');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return back()->with('success', 'تم حذف الوسم');
    }
}
