<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('parent')->withCount('posts')->orderBy('sort_order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:categories,slug',
            'parent_id'        => 'nullable|exists:categories,id',
            'description'      => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'sort_order'       => 'nullable|integer',
            'image'            => 'nullable|image|max:2048',
        ], ['name.required' => 'اسم التصنيف مطلوب']);

        $slug = $request->slug ?: Str::slug($request->name);
        if (Category::where('slug', $slug)->exists()) {
            $slug .= '-' . time();
        }

        $data = $request->except(['_token', 'image']);
        $data['slug'] = $slug;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);
        Cache::forget('categories_list');

        return redirect()->route('admin.categories.index')->with('success', 'تم إضافة التصنيف');
    }

    public function edit(Category $category)
    {
        $parents = Category::whereNull('parent_id')->where('id', '!=', $category->id)->orderBy('name')->get();
        return view('admin.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'parent_id'        => 'nullable|exists:categories,id',
            'description'      => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'sort_order'       => 'nullable|integer',
            'image'            => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['_token', '_method', 'image']);
        $data['slug'] = $request->slug ?: Str::slug($request->name);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);
        Cache::forget('categories_list');

        return redirect()->route('admin.categories.index')->with('success', 'تم تحديث التصنيف');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        Cache::forget('categories_list');
        return redirect()->route('admin.categories.index')->with('success', 'تم حذف التصنيف');
    }
}
