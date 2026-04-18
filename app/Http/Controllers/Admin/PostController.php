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
                'posts', 250, 200
            );
        } elseif ($request->filled('featured_image_library')) {
            $validated['featured_image'] = $request->input('featured_image_library');
        }

        if ($request->hasFile('banner_image')) {
            $validated['banner_image'] = ImageService::uploadWebP(
                $request->file('banner_image'),
                'posts', 743, 418
            );
        } elseif ($request->filled('banner_image_library')) {
            $validated['banner_image'] = $request->input('banner_image_library');
        }

        if ($request->hasFile('gallery_images')) {
            $gallery = [];
            foreach ($request->file('gallery_images') as $img) {
                $gallery[] = ImageService::uploadWebP($img, 'gallery', 743, 418);
            }
            $validated['gallery_images'] = $gallery;
        }

        if ($request->status === 'published' && empty($request->published_at)) {
            $validated['published_at'] = now();
        }

        $post = Post::create($validated);

        if ($request->filled('tags')) {
            $tagIds = $this->syncTags($request->tags);
            $post->tags()->sync($tagIds);
        }

        if ($request->has('download_links')) {
            foreach ($request->download_links as $i => $linkData) {
                if (!empty($linkData['url'])) {
                    $post->downloadLinks()->create([
                        'label'      => $linkData['label'] ?? 'تحميل مباشر',
                        'url'        => $linkData['url'],
                        'platform'   => $linkData['platform'] ?? 'pc',
                        'file_size'  => $linkData['file_size'] ?? null,
                        'version'    => $linkData['version'] ?? null,
                        'sort_order' => $i,
                    ]);
                }
            }
        }

        $this->clearPostCaches();

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

        // Never overwrite image fields with null — only update when a new file is uploaded
        unset($validated['featured_image'], $validated['banner_image'], $validated['gallery_images']);

        if ($request->slug !== $post->slug) {
            $validated['slug'] = $this->generateSlug($request->slug ?: $request->title, $post->id);
        }

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                ImageService::delete($post->featured_image);
            }
            $validated['featured_image'] = ImageService::uploadWebP(
                $request->file('featured_image'),
                'posts', 250, 200
            );
        } elseif ($request->filled('featured_image_library')) {
            $validated['featured_image'] = $request->input('featured_image_library');
        }

        if ($request->hasFile('banner_image')) {
            if ($post->banner_image) {
                ImageService::delete($post->banner_image);
            }
            $validated['banner_image'] = ImageService::uploadWebP(
                $request->file('banner_image'),
                'posts', 743, 418
            );
        } elseif ($request->filled('banner_image_library')) {
            $validated['banner_image'] = $request->input('banner_image_library');
        }

        if ($request->hasFile('gallery_images')) {
            $gallery = $post->gallery_images ?? [];
            foreach ($request->file('gallery_images') as $img) {
                $gallery[] = ImageService::uploadWebP($img, 'gallery', 743, 418);
            }
            $validated['gallery_images'] = $gallery;
        } elseif ($request->boolean('clear_gallery')) {
            if ($post->gallery_images) {
                foreach ($post->gallery_images as $img) {
                    ImageService::delete($img);
                }
            }
            $validated['gallery_images'] = null;
        }

        // Set published_at when publishing for the first time
        if ($request->status === 'published' && empty($validated['published_at']) && empty($post->published_at)) {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        if ($request->has('tags')) {
            $tagIds = $this->syncTags($request->tags ?? []);
            $post->tags()->sync($tagIds);
        }

        if ($request->has('new_download_links')) {
            $sortOffset = $post->downloadLinks()->count();
            foreach ($request->new_download_links as $i => $linkData) {
                if (!empty($linkData['url'])) {
                    $post->downloadLinks()->create([
                        'label'      => $linkData['label'] ?? 'تحميل مباشر',
                        'url'        => $linkData['url'],
                        'file_size'  => $linkData['file_size'] ?? null,
                        'version'    => $linkData['version'] ?? null,
                        'sort_order' => $sortOffset + $i,
                    ]);
                }
            }
        }

        $this->clearPostCaches();

        return back()->with('success', 'تم تحديث المقال بنجاح');
    }

    public function destroy(Post $post)
    {
        if ($post->featured_image) {
            ImageService::delete($post->featured_image);
        }
        $post->delete();
        $this->clearPostCaches();

        return redirect()->route('admin.posts.index')->with('success', 'تم حذف المقال');
    }

    public function publish(Post $post)
    {
        $post->update([
            'status'       => 'published',
            'published_at' => $post->published_at ?? now(),
        ]);
        $this->clearPostCaches();

        return back()->with('success', 'تم نشر المقال');
    }

    public function addGalleryImages(Request $request, Post $post)
    {
        $request->validate(['images' => 'required|array', 'images.*' => 'image|max:5120']);

        $gallery = $post->gallery_images ?? [];
        foreach ($request->file('images') as $img) {
            $gallery[] = ImageService::uploadWebP($img, 'gallery', 743, 418);
        }
        $post->update(['gallery_images' => $gallery]);
        $this->clearPostCaches();

        return back()->with('success', 'تم إضافة الصور بنجاح');
    }

    public function removeGalleryImage(Post $post, int $index)
    {
        $gallery = $post->gallery_images ?? [];
        if (isset($gallery[$index])) {
            ImageService::delete($gallery[$index]);
            array_splice($gallery, $index, 1);
            $post->update(['gallery_images' => array_values($gallery) ?: null]);
            $this->clearPostCaches();
        }

        return back()->with('success', 'تم حذف الصورة');
    }

    public function removeImage(Post $post, string $field)
    {
        if (! in_array($field, ['featured_image', 'banner_image'])) {
            abort(404);
        }
        if ($post->$field) {
            ImageService::delete($post->$field);
            $post->update([$field => null]);
            $this->clearPostCaches();
        }

        return back()->with('success', 'تم حذف الصورة');
    }

    private function clearPostCaches(): void
    {
        $keys = [
            'home_trending',
            'home_latest_software',
            'home_latest_apks',
            'home_latest_articles',
            'home_categories',
            'home_latest_all',
            'sidebar_trending',
            'sidebar_tags',
        ];
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    private function validatePost(Request $request): array
    {
        return $request->validate([
            'title'               => 'required|string|max:255',
            'game_name'           => 'nullable|string|max:255',
            'game_url'            => 'nullable|url|max:500',
            'slug'                => 'nullable|string|max:255',
            'excerpt'             => 'nullable|string',
            'content'             => 'nullable|string',
            'type'                => 'required|in:game,software,apk,blog,tutorial,listicle,review',
            'status'              => 'required|in:draft,pending,published,scheduled',
            'featured_image'      => 'nullable|image|max:5120',
            'banner_image'        => 'nullable|image|max:5120',
            'gallery_images'      => 'nullable|array',
            'gallery_images.*'    => 'image|max:5120',
            'version'             => 'nullable|string|max:50',
            'developer'           => 'nullable|string|max:255',
            'developer_url'       => 'nullable|url|max:500',
            'publisher'           => 'nullable|string|max:255',
            'publisher_url'       => 'nullable|url|max:500',
            'category_url'        => 'nullable|url|max:500',
            'file_size'           => 'nullable|string|max:50',
            'game_language'       => 'nullable|string|max:100',
            'platform'            => 'required|in:pc,android,ios,mac,all',
            'release_date'        => 'nullable|date',
            'updated_date'        => 'nullable|date',
            'system_requirements' => 'nullable|string',
            'sys_req_os'          => 'nullable|string|max:255',
            'sys_req_cpu'         => 'nullable|string|max:255',
            'sys_req_gpu'         => 'nullable|string|max:255',
            'sys_req_ram'         => 'nullable|string|max:255',
            'sys_req_storage'     => 'nullable|string|max:255',
            'sys_req_software'    => 'nullable|string|max:255',
            'features'            => 'nullable|string',
            'whats_new'           => 'nullable|string',
            'pros'                => 'nullable|string',
            'cons'                => 'nullable|string',
            'youtube_url'         => 'nullable|url',
            'meta_title'          => 'nullable|string|max:255',
            'meta_description'    => 'nullable|string|max:500',
            'meta_keywords'       => 'nullable|string|max:500',
            'focus_keyword'       => 'nullable|string|max:255',
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
