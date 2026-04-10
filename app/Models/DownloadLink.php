<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownloadLink extends Model
{
    protected $fillable = [
        'post_id', 'label', 'url', 'platform', 'file_size', 'version', 'sort_order', 'clicks',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
