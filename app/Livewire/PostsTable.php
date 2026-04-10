<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PostsTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $type = '';

    #[Url]
    public string $status = '';

    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';

    public function updatedSearch(): void  { $this->resetPage(); }
    public function updatedType(): void    { $this->resetPage(); }
    public function updatedStatus(): void  { $this->resetPage(); }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'desc';
        }
        $this->resetPage();
    }

    public function publish(int $id): void
    {
        $post = Post::findOrFail($id);
        $post->update([
            'status'       => 'published',
            'published_at' => $post->published_at ?? now(),
        ]);
        \Illuminate\Support\Facades\Cache::flush();
        session()->flash('success', 'تم نشر المقال بنجاح');
    }

    public function delete(int $id): void
    {
        $post = Post::findOrFail($id);
        if ($post->featured_image) {
            \App\Services\ImageService::delete($post->featured_image);
        }
        $post->delete();
        \Illuminate\Support\Facades\Cache::flush();
        session()->flash('success', 'تم حذف المقال');
    }

    public function render()
    {
        $query = Post::with(['category', 'author'])->orderBy($this->sortBy, $this->sortDir);

        if ($this->search !== '') {
            $query->where('title', 'like', '%' . $this->search . '%');
        }
        if ($this->type !== '') {
            $query->where('type', $this->type);
        }
        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        return view('livewire.posts-table', [
            'posts' => $query->paginate(20),
        ]);
    }
}
