<?php

namespace App\Livewire;

use App\Models\Media;
use App\Services\ImageService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MediaLibrary extends Component
{
    use WithFileUploads, WithPagination;

    public $uploads = [];
    public string $altText = '';
    public bool $uploading = false;
    public int $uploadProgress = 0;

    public function updatedUploads(): void
    {
        $this->validate([
            'uploads.*' => 'image|max:10240',
        ], [
            'uploads.*.image' => 'الملف يجب أن يكون صورة',
            'uploads.*.max'   => 'حجم الصورة يتجاوز 10 ميغابايت',
        ]);
    }

    public function uploadFiles(): void
    {
        $this->uploading = true;

        foreach ($this->uploads as $file) {
            $path = ImageService::uploadWebP($file, 'media');

            Media::create([
                'filename'      => basename($path),
                'original_name' => $file->getClientOriginalName(),
                'path'          => $path,
                'alt_text'      => $this->altText ?: null,
                'size'          => $file->getSize(),
                'mime_type'     => 'image/webp',
                'uploaded_by'   => auth()->id(),
            ]);
        }

        $this->uploads  = [];
        $this->altText  = '';
        $this->uploading = false;
        $this->resetPage();
        session()->flash('success', 'تم رفع الصور بنجاح');
    }

    public function updateAlt(int $id, string $alt): void
    {
        Media::findOrFail($id)->update(['alt_text' => $alt]);
    }

    public function delete(int $id): void
    {
        $media = Media::findOrFail($id);
        Storage::disk('public')->delete($media->path);
        $media->delete();
        session()->flash('success', 'تم حذف الصورة');
    }

    public function copyUrl(string $path): void
    {
        $this->dispatch('copy-url', url: asset('storage/' . $path));
    }

    public function render()
    {
        return view('livewire.media-library', [
            'mediaItems' => Media::with('uploader')->orderByDesc('created_at')->paginate(24),
        ]);
    }
}
