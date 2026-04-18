<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('admin.profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'             => 'required|string|max:100',
            'email'            => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'required_with:new_password',
            'new_password'     => 'nullable|min:8|confirmed',
        ], [
            'name.required'                  => 'الاسم مطلوب',
            'email.required'                 => 'البريد الإلكتروني مطلوب',
            'email.unique'                   => 'البريد الإلكتروني مستخدم بالفعل',
            'current_password.required_with' => 'يجب إدخال كلمة المرور الحالية لتغييرها',
            'new_password.min'               => 'كلمة المرور الجديدة يجب أن تكون 8 أحرف على الأقل',
            'new_password.confirmed'         => 'تأكيد كلمة المرور غير متطابق',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة'])->withInput();
            }
            $data['password'] = Hash::make($request->new_password);
        }

        $user->update($data);

        return back()->with('success', 'تم تحديث بيانات الحساب بنجاح');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ], [
            'avatar.required' => 'اختر صورة أولاً',
            'avatar.image'    => 'الملف يجب أن يكون صورة',
            'avatar.max'      => 'حجم الصورة يتجاوز 2 ميغابايت',
        ]);

        $user = auth()->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = ImageService::uploadWebP($request->file('avatar'), 'avatars');
        $user->update(['avatar' => $path]);

        return back()->with('success', 'تم تحديث الصورة الشخصية بنجاح');
    }

    public function removeAvatar()
    {
        $user = auth()->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return back()->with('success', 'تم حذف الصورة الشخصية');
    }
}
