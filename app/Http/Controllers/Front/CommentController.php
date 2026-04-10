<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'name'    => 'required|string|max:100',
            'email'   => 'nullable|email|max:255',
            'body'    => 'required|string|min:10|max:1000',
        ], [
            'post_id.required' => 'المقال غير محدد',
            'name.required'    => 'الاسم مطلوب',
            'body.required'    => 'نص التعليق مطلوب',
            'body.min'         => 'التعليق يجب أن يكون 10 أحرف على الأقل',
            'body.max'         => 'التعليق طويل جداً',
        ]);

        Comment::create([
            'post_id'    => $request->post_id,
            'name'       => strip_tags($request->name),
            'email'      => $request->email,
            'body'       => strip_tags($request->body),
            'status'     => 'pending',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('comment_success', 'تم إرسال تعليقك وسيظهر بعد المراجعة');
    }
}
