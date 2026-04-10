<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['category', 'author'])
            ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $posts = $query->paginate(20);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePost($request);

        $slug = $this->generateSlug($request->slug ?: $request->title);
        $validated['slug'] = $slug;
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = ImageService::uploadWebP(
                $request->file('featured_image'),
                'posts'
            );
        }

        if ($request->status === 'published' && empty($request->published_at)) {
            $validated['published_at'] = now();
        }

        $post = Post::create($validated);

        if ($request->filled('tags')) {
            $tagIds = $this->syncTags($request->tags);
            $post->tags()->sync($tagIds);
        }

        Cache::flush();

        return redirect()->route('admin.posts.edit', $post)
            ->with('success', 'تم حفظ المقال بنجاح');
    }

    public function edit(Post $post)
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $post->load(['tags', 'downloadLinks']);
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $this->validatePost($request);

        if ($request->slug !== $post->slug) {
            $validated['slug'] = $this->generateSlug($request->slug ?: $request->title, $post->id);
        }

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                ImageService::delete($post->featured_image);
            }
            $validated['featured_image'] = ImageService::uploadWebP(
                $request->file('featured_image'),
                'posts'
            );
        }

        if ($request->status === 'published' && empty($post->published_at)) {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        if ($request->has('tags')) {
            $tagIds = $this->syncTags($request->tags ?? []);
            $post->tags()->sync($tagIds);
        }

        Cache::flush();

        return back()->with('success', 'تم تحديث المقال بنجاح');
    }

    public function destroy(Post $post)
    {
        if ($post->featured_image) {
            ImageService::delete($post->featured_image);
        }
        $post->delete();
        Cache::flush();

        return redirect()->route('admin.posts.index')->with('success', 'تم حذف المقال');
    }

    public function publish(Post $post)
    {
        $post->update([
            'status'       => 'published',
            'published_at' => $post->published_at ?? now(),
        ]);
        Cache::flush();

        return back()->with('success', 'تم نشر المقال');
    }

    private function validatePost(Request $request): array
    {
        return $request->validate([
            'title'               => 'required|string|max:255',
            'slug'                => 'nullable|string|max:255',
            'excerpt'             => 'nullable|string',
            'content'             => 'nullable|string',
            'type'                => 'required|in:game,software,apk,blog,tutorial,listicle,review',
            'status'              => 'required|in:draft,pending,published,scheduled',
            'featured_image'      => 'nullable|image|max:5120',
            'version'             => 'nullable|string|max:50',
            'developer'           => 'nullable|string|max:255',
            'file_size'           => 'nullable|string|max:50',
            'platform'            => 'required|in:pc,android,ios,mac,all',
            'release_date'        => 'nullable|date',
            'updated_date'        => 'nullable|date',
            'system_requirements' => 'nullable|string',
            'features'            => 'nullable|string',
            'whats_new'           => 'nullable|string',
            'pros'                => 'nullable|string',
            'cons'                => 'nullable|string',
            'youtube_url'         => 'nullable|url',
            'meta_title'          => 'nullable|string|max:255',
            'meta_description'    => 'nullable|string|max:500',
            'meta_keywords'       => 'nullable|string|max:500',
            'canonical_url'       => 'nullable|url',
            'robots'              => 'nullable|string|max:100',
            'schema_type'         => 'required|in:SoftwareApplication,Article,Review,HowTo,ItemList',
            'og_title'            => 'nullable|string|max:255',
            'og_description'      => 'nullable|string',
            'category_id'         => 'nullable|exists:categories,id',
            'published_at'        => 'nullable|date',
        ], [
            'title.required' => 'عنوان المقال مطلوب',
            'type.required'  => 'نوع المحتوى مطلوب',
        ]);
    }

    private function generateSlug(string $text, int $excludeId = 0): string
    {
        $slug = Str::slug($text);
        if (empty($slug)) {
            $slug = 'post-' . time();
        }

        $original = $slug;
        $count = 1;
        while (Post::where('slug', $slug)->where('id', '!=', $excludeId)->exists()) {
            $slug = $original . '-' . $count++;
        }
        return $slug;
    }

    private function syncTags(array|string $tags): array
    {
        if (is_string($tags)) {
            $tags = array_filter(array_map('trim', explode(',', $tags)));
        }

        $ids = [];
        foreach ($tags as $tagName) {
            if (is_numeric($tagName)) {
                $ids[] = (int) $tagName;
            } else {
                $slug = Str::slug($tagName);
                $tag = Tag::firstOrCreate(['slug' => $slug], ['name' => $tagName]);
                $ids[] = $tag->id;
            }
        }
        return $ids;
    }
}
