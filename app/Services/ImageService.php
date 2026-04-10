<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    public static function uploadWebP(UploadedFile $file, string $directory = 'uploads'): string
    {
        $filename = Str::uuid() . '.webp';
        $path = $directory . '/' . $filename;

        $image = Image::read($file->getRealPath());

        // Create large version (800x450)
        $large = clone $image;
        $large->cover(800, 450);

        Storage::disk('public')->put($path, $large->toWebp(85)->toString());

        // Create thumbnail (400x225)
        $thumbPath = $directory . '/thumbs/' . $filename;
        $thumb = clone $image;
        $thumb->cover(400, 225);
        Storage::disk('public')->put($thumbPath, $thumb->toWebp(80)->toString());

        return $path;
    }

    public static function delete(string $path): void
    {
        Storage::disk('public')->delete($path);
        $thumbPath = str_replace('/', '/thumbs/', $path, 1);
        Storage::disk('public')->delete($thumbPath);
    }

    public static function getThumbUrl(string $path): string
    {
        $parts = explode('/', $path, 2);
        if (count($parts) === 2) {
            return asset('storage/' . $parts[0] . '/thumbs/' . $parts[1]);
        }
        return asset('storage/' . $path);
    }
}
