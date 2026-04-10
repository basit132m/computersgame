<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedIp extends Model
{
    public $timestamps = false;

    protected $fillable = ['ip_address', 'reason', 'blocked_at'];

    protected function casts(): array
    {
        return [
            'blocked_at' => 'datetime',
        ];
    }
}
