<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    public static function uploadWebP(UploadedFile $file, string $directory = 'uploads', int $width = 0, int $height = 0): string
    {
        $ext      = $file->getClientOriginalExtension() ?: 'webp';
        $filename = Str::uuid() . '.' . $ext;
        $path     = $directory . '/' . $filename;

        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        return $path;
    }

    public static function delete(string $path): void
    {
        Storage::disk('public')->delete($path);
    }

    public static function getThumbUrl(string $path): string
    {
        return asset('storage/' . $path);
    }
}
