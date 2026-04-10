<?php

namespace App\Livewire;

use App\Models\Comment;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CommentsTable extends Component
{
    use WithPagination;

    #[Url]
    public string $status = 'pending';

    #[Url(as: 'q')]
    public string $search = '';

    public array $selected = [];
    public bool $selectAll = false;

    public function updatedSearch(): void  { $this->resetPage(); $this->selected = []; }
    public function updatedStatus(): void  { $this->resetPage(); $this->selected = []; }

    public function approve(int $id): void
    {
        Comment::findOrFail($id)->update(['status' => 'approved']);
        session()->flash('success', 'تمت الموافقة على التعليق');
    }

    public function reject(int $id): void
    {
        Comment::findOrFail($id)->update(['status' => 'rejected']);
        session()->flash('success', 'تم رفض التعليق');
    }

    public function delete(int $id): void
    {
        Comment::findOrFail($id)->delete();
        $this->selected = array_filter($this->selected, fn($s) => $s !== $id);
        session()->flash('success', 'تم حذف التعليق');
    }

    public function bulkApprove(): void
    {
        Comment::whereIn('id', $this->selected)->update(['status' => 'approved']);
        $this->selected = [];
        session()->flash('success', 'تمت الموافقة على التعليقات المحددة');
    }

    public function bulkReject(): void
    {
        Comment::whereIn('id', $this->selected)->update(['status' => 'rejected']);
        $this->selected = [];
        session()->flash('success', 'تم رفض التعليقات المحددة');
    }

    public function bulkDelete(): void
    {
        Comment::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        session()->flash('success', 'تم حذف التعليقات المحددة');
    }

    public function render()
    {
        $query = Comment::with('post')->latest();

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }
        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('body', 'like', '%' . $this->search . '%')
                  ->orWhere('name', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.comments-table', [
            'comments' => $query->paginate(20),
        ]);
    }
}
