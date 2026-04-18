<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Rating;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    public function show(Post $post)
    {
        if ($post->status !== 'published') {
            abort(404);
        }

        $post->load(['category', 'tags', 'downloadLinks', 'approvedComments', 'ratings']);

        // Increment views (once per IP per hour)
        $cacheKey = 'post_view_' . $post->id . '_' . request()->ip();
        if (!Cache::has($cacheKey)) {
            $post->increment('views');
            Cache::put($cacheKey, true, 3600);
        }

        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->latest('published_at')
            ->limit(6)
            ->get();

        $sidebarTrending = Cache::remember('sidebar_trending', 3600, function () {
            return Post::published()->orderByDesc('downloads')->limit(10)->get();
        });

        $sidebarTags = Cache::remember('sidebar_tags', 21600, function () {
            return Tag::withCount('posts')->orderByDesc('posts_count')->limit(20)->get();
        });

        $userRating = Rating::where('post_id', $post->id)
            ->where('ip_address', request()->ip())
            ->value('score');

        $schemaData = $this->buildSchema($post);

        return view('front.post', compact(
            'post', 'relatedPosts', 'sidebarTrending', 'sidebarTags', 'userRating', 'schemaData'
        ));
    }

    private function buildSchema(Post $post): array
    {
        $base = [
            '@context' => 'https://schema.org',
            'name'     => $post->title,
            'url'      => url($post->slug),
        ];

        if ($post->average_rating > 0) {
            $base['aggregateRating'] = [
                '@type'       => 'AggregateRating',
                'ratingValue' => $post->average_rating,
                'ratingCount' => $post->ratings_count,
                'bestRating'  => 5,
                'worstRating' => 1,
            ];
        }

        $image = $post->og_image ?? $post->banner_image ?? $post->featured_image;

        $orgName = 'ألعاب الكمبيوتر';
        $downloadUrl = $post->downloadLinks->first()?->url;

        return match ($post->schema_type) {
            'SoftwareApplication' => array_merge($base, array_filter([
                '@type'               => 'SoftwareApplication',
                'applicationCategory' => $post->category?->name,
                'operatingSystem'     => $post->platform_ar,
                'softwareVersion'     => $post->version,
                'fileSize'            => $post->file_size,
                'description'         => $post->excerpt ? strip_tags($post->excerpt) : null,
                'image'               => $image ? asset('storage/' . $image) : null,
                'datePublished'       => $post->published_at?->toIso8601String(),
                'dateModified'        => $post->updated_at?->toIso8601String(),
                'author'              => ['@type' => 'Organization', 'name' => $post->developer ?: $orgName],
                'publisher'           => ['@type' => 'Organization', 'name' => $orgName],
                'downloadUrl'         => $downloadUrl,
                'potentialAction'     => $downloadUrl ? ['@type' => 'DownloadAction', 'target' => $downloadUrl] : null,
                'offers'              => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'SAR', 'availability' => 'https://schema.org/InStock'],
            ])),
            'Article' => array_merge($base, array_filter([
                '@type'         => 'Article',
                'headline'      => $post->title,
                'description'   => $post->excerpt ? strip_tags($post->excerpt) : null,
                'image'         => $image ? asset('storage/' . $image) : null,
                'datePublished' => $post->published_at?->toIso8601String(),
                'dateModified'  => $post->updated_at?->toIso8601String(),
                'author'        => ['@type' => 'Organization', 'name' => $orgName],
                'publisher'     => ['@type' => 'Organization', 'name' => $orgName],
            ])),
            default => $base,
        };
    }
}
