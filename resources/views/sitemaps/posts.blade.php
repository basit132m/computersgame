<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    @foreach($posts as $post)
    <url>
        <loc>{{ url($post->slug) }}</loc>
        <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>@if($post->views > 1000)0.9@elseif($post->views > 100)0.8@elseif($post->type === 'blog')0.5@else0.7@endif</priority>
        @if($post->featured_image)
        <image:image>
            <image:loc>{{ asset('storage/' . $post->featured_image) }}</image:loc>
            <image:title>{{ $post->title }}</image:title>
        </image:image>
        @endif
    </url>
    @endforeach
</urlset>
