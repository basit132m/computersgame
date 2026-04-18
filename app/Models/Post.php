<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $fillable = [
        'title', 'game_name', 'game_url', 'slug', 'excerpt', 'content', 'type', 'status',
        'featured_image', 'banner_image', 'gallery_images',
        'version', 'developer', 'developer_url', 'publisher', 'publisher_url', 'file_size', 'game_language', 'platform',
        'release_date', 'updated_date', 'system_requirements', 'features',
        'sys_req_os', 'sys_req_cpu', 'sys_req_gpu', 'sys_req_ram', 'sys_req_storage', 'sys_req_software',
        'whats_new', 'pros', 'cons', 'youtube_url', 'meta_title',
        'meta_description', 'meta_keywords', 'focus_keyword', 'canonical_url', 'robots',
        'schema_type', 'og_title', 'og_description', 'og_image',
        'views', 'downloads', 'published_at', 'created_by', 'category_id', 'category_url',
    ];

    protected function casts(): array
    {
        return [
            'release_date'   => 'date',
            'updated_date'   => 'date',
            'published_at'   => 'datetime',
            'gallery_images' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    public function downloadLinks(): HasMany
    {
        return $this->hasMany(DownloadLink::class)->orderBy('sort_order');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('status', 'approved');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function downloadClicks(): HasMany
    {
        return $this->hasMany(DownloadClick::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getAverageRatingAttribute(): float
    {
        return round($this->ratings()->avg('score') ?? 0, 1);
    }

    public function getRatingsCountAttribute(): int
    {
        return $this->ratings()->count();
    }

    public function getTypeArAttribute(): string
    {
        return match ($this->type) {
            'game' => 'لعبة',
            'software' => 'برنامج',
            'apk' => 'تطبيق',
            'blog' => 'مقال',
            'tutorial' => 'شرح',
            'listicle' => 'قائمة',
            'review' => 'مراجعة',
            default => $this->type,
        };
    }

    public function getPlatformArAttribute(): string
    {
        return match ($this->platform) {
            'pc' => 'كمبيوتر',
            'android' => 'أندرويد',
            'ios' => 'iOS',
            'mac' => 'ماك',
            'all' => 'جميع المنصات',
            default => $this->platform,
        };
    }

    public function scopePublished($query)
    {
        return $query->where(function ($q) {
            // status=published is always live regardless of published_at date
            $q->where('status', 'published')
              // status=scheduled only goes live once the publish date has passed
              ->orWhere(function ($q2) {
                  $q2->where('status', 'scheduled')
                     ->where('published_at', '<=', now());
              });
        });
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
