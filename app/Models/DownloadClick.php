<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownloadClick extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'post_id', 'link_id', 'ip_address', 'user_agent', 'referer', 'source', 'clicked_at',
    ];

    protected function casts(): array
    {
        return [
            'clicked_at' => 'datetime',
        ];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(DownloadLink::class);
    }
}
