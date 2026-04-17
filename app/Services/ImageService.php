<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    public static function uploadWebP(UploadedFile $file, string $directory = 'uploads'): string
    {
        $realPath = $file->getRealPath();
        $uuid     = Str::uuid();

        $source = self::createGdSource($realPath, $file->getMimeType());

        if ($source) {
            $filename = $uuid . '.webp';
            $path     = $directory . '/' . $filename;

            $large = self::coverCrop($source, 800, 450);
            Storage::disk('public')->put($path, self::toWebP($large, 85));
            imagedestroy($large);

            $thumb = self::coverCrop($source, 400, 225);
            Storage::disk('public')->put($directory . '/thumbs/' . $filename, self::toWebP($thumb, 80));
            imagedestroy($thumb);

            imagedestroy($source);
            return $path;
        }

        // Fallback: save original file unchanged
        $ext      = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = $uuid . '.' . $ext;
        $path     = $directory . '/' . $filename;
        Storage::disk('public')->put($path, file_get_contents($realPath));
        return $path;
    }

    public static function delete(string $path): void
    {
        Storage::disk('public')->delete($path);
        $parts = explode('/', $path, 2);
        if (count($parts) === 2) {
            Storage::disk('public')->delete($parts[0] . '/thumbs/' . $parts[1]);
        }
    }

    public static function getThumbUrl(string $path): string
    {
        $parts = explode('/', $path, 2);
        if (count($parts) === 2) {
            $thumb = $parts[0] . '/thumbs/' . $parts[1];
            if (Storage::disk('public')->exists($thumb)) {
                return asset('storage/' . $thumb);
            }
        }
        return asset('storage/' . $path);
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private static function createGdSource(string $path, ?string $mime): mixed
    {
        if (! function_exists('imagecreatefromjpeg')) {
            return null;
        }
        return match(true) {
            str_contains((string)$mime, 'png')  => @imagecreatefrompng($path),
            str_contains((string)$mime, 'gif')  => @imagecreatefromgif($path),
            str_contains((string)$mime, 'webp') => @imagecreatefromwebp($path),
            default                              => @imagecreatefromjpeg($path),
        };
    }

    private static function coverCrop(\GdImage $source, int $w, int $h): \GdImage
    {
        $sw = imagesx($source);
        $sh = imagesy($source);

        if ($sw / $sh > $w / $h) {
            $cropW = (int) round($sh * $w / $h);
            $cropH = $sh;
            $cropX = (int) round(($sw - $cropW) / 2);
            $cropY = 0;
        } else {
            $cropW = $sw;
            $cropH = (int) round($sw * $h / $w);
            $cropX = 0;
            $cropY = (int) round(($sh - $cropH) / 2);
        }

        $dest = imagecreatetruecolor($w, $h);
        imagealphablending($dest, false);
        imagesavealpha($dest, true);
        imagefill($dest, 0, 0, imagecolorallocatealpha($dest, 0, 0, 0, 127));
        imagealphablending($dest, true);
        imagecopyresampled($dest, $source, 0, 0, $cropX, $cropY, $w, $h, $cropW, $cropH);

        return $dest;
    }

    private static function toWebP(\GdImage $img, int $quality): string
    {
        ob_start();
        imagewebp($img, null, $quality);
        return ob_get_clean();
    }
}
