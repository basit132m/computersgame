<?php

namespace App\Livewire;

use App\Models\DownloadLink;
use App\Models\Post;
use Livewire\Attributes\On;
use Livewire\Component;

class DownloadLinksReorder extends Component
{
    public int $postId;
    public array $links = [];

    public string $newLabel    = '';
    public string $newUrl      = '';
    public string $newPlatform = '';
    public string $newFileSize = '';
    public string $newVersion  = '';

    public function mount(int $postId): void
    {
        $this->postId = $postId;
        $this->loadLinks();
    }

    public function loadLinks(): void
    {
        $this->links = DownloadLink::where('post_id', $this->postId)
            ->orderBy('sort_order')
            ->get()
            ->toArray();
    }

    public function addLink(): void
    {
        $this->validate([
            'newLabel' => 'required|string|max:100',
            'newUrl'   => 'required|url',
        ], [
            'newLabel.required' => 'عنوان الرابط مطلوب',
            'newUrl.required'   => 'رابط التحميل مطلوب',
            'newUrl.url'        => 'الرابط غير صحيح',
        ]);

        DownloadLink::create([
            'post_id'    => $this->postId,
            'label'      => $this->newLabel,
            'url'        => $this->newUrl,
            'platform'   => $this->newPlatform ?: null,
            'file_size'  => $this->newFileSize ?: null,
            'version'    => $this->newVersion ?: null,
            'sort_order' => count($this->links),
        ]);

        $this->newLabel    = '';
        $this->newUrl      = '';
        $this->newPlatform = '';
        $this->newFileSize = '';
        $this->newVersion  = '';

        $this->loadLinks();
        session()->flash('success', 'تم إضافة رابط التحميل');
    }

    public function delete(int $id): void
    {
        DownloadLink::findOrFail($id)->delete();
        $this->loadLinks();
        session()->flash('success', 'تم حذف الرابط');
    }

    #[On('reorder')]
    public function reorder(array $order): void
    {
        foreach ($order as $index => $id) {
            DownloadLink::where('id', $id)->update(['sort_order' => $index]);
        }
        $this->loadLinks();
    }

    public function render()
    {
        return view('livewire.download-links-reorder');
    }
}
