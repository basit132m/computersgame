<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ], [
            'name.required'          => 'الاسم مطلوب',
            'email.required'         => 'البريد الإلكتروني مطلوب',
            'email.unique'           => 'البريد الإلكتروني مستخدم بالفعل',
            'current_password.required_with' => 'يجب إدخال كلمة المرور الحالية لتغييرها',
            'new_password.min'       => 'كلمة المرور الجديدة يجب أن تكون 8 أحرف على الأقل',
            'new_password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
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
}
