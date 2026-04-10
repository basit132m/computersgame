<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $media = Media::with('uploader')
            ->orderByDesc('created_at')
            ->paginate(24);
        return view('admin.media.index', compact('media'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file'     => 'required|file|mimes:jpg,jpeg,png,gif,webp,svg|max:10240',
            'alt_text' => 'nullable|string|max:255',
        ], [
            'file.required' => 'الملف مطلوب',
            'file.mimes'    => 'نوع الملف غير مدعوم',
            'file.max'      => 'حجم الملف يتجاوز 10 ميغابايت',
        ]);

        $file = $request->file('file');
        $path = ImageService::uploadWebP($file, 'media');

        $media = Media::create([
            'filename'      => basename($path),
            'original_name' => $file->getClientOriginalName(),
            'path'          => $path,
            'alt_text'      => $request->alt_text,
            'size'          => $file->getSize(),
            'mime_type'     => 'image/webp',
            'uploaded_by'   => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'url'     => asset('storage/' . $path),
            'media'   => $media,
        ]);
    }

    public function updateAlt(Request $request, Media $media)
    {
        $request->validate(['alt_text' => 'required|string|max:255']);
        $media->update(['alt_text' => $request->alt_text]);
        return response()->json(['success' => true]);
    }

    public function destroy(Media $media)
    {
        Storage::disk('public')->delete($media->path);
        $media->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'تم حذف الملف');
    }
}
