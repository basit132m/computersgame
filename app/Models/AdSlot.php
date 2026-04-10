<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdSlot extends Model
{
    protected $fillable = ['slot_key', 'label', 'code', 'active'];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public static function getCode(string $key): ?string
    {
        $slot = static::where('slot_key', $key)->where('active', true)->first();
        return $slot ? $slot->code : null;
    }
}
