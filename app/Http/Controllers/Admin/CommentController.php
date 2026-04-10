<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = Comment::with('post')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('body', 'like', '%' . $request->search . '%');
        }

        $comments = $query->paginate(20);
        return view('admin.comments.index', compact('comments'));
    }

    public function approve(Comment $comment)
    {
        $comment->update(['status' => 'approved']);
        return back()->with('success', 'تمت الموافقة على التعليق');
    }

    public function reject(Comment $comment)
    {
        $comment->update(['status' => 'rejected']);
        return back()->with('success', 'تم رفض التعليق');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return back()->with('success', 'تم حذف التعليق');
    }

    public function bulk(Request $request)
    {
        $request->validate([
            'action'   => 'required|in:approve,reject,delete',
            'ids'      => 'required|array',
            'ids.*'    => 'integer',
        ]);

        $comments = Comment::whereIn('id', $request->ids);

        match ($request->action) {
            'approve' => $comments->update(['status' => 'approved']),
            'reject'  => $comments->update(['status' => 'rejected']),
            'delete'  => $comments->delete(),
        };

        return back()->with('success', 'تم تنفيذ الإجراء بنجاح');
    }
}
