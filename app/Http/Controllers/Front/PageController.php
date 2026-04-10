<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('front.pages.about');
    }

    public function contact()
    {
        return view('front.pages.contact');
    }

    public function sendContact(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:20|max:2000',
        ], [
            'name.required'    => 'الاسم مطلوب',
            'email.required'   => 'البريد الإلكتروني مطلوب',
            'email.email'      => 'البريد الإلكتروني غير صحيح',
            'subject.required' => 'الموضوع مطلوب',
            'message.required' => 'الرسالة مطلوبة',
            'message.min'      => 'الرسالة قصيرة جداً',
        ]);

        // Log contact (send email in production)
        \Illuminate\Support\Facades\Log::info('Contact form submission', $request->only(['name', 'email', 'subject']));

        return back()->with('contact_success', 'تم إرسال رسالتك بنجاح! سنرد عليك قريباً.');
    }

    public function privacy()
    {
        return view('front.pages.privacy');
    }

    public function terms()
    {
        return view('front.pages.terms');
    }
}
