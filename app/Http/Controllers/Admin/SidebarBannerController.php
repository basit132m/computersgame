<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SidebarBanner;
use App\Services\ImageService;
use Illuminate\Http\Request;

class SidebarBannerController extends Controller
{
    public function index()
    {
        $banners = SidebarBanner::orderBy('sort_order')->orderBy('id')->get();
        return view('admin.sidebar-banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:4096',
            'url'   => 'required|url|max:500',
            'title' => 'nullable|string|max:255',
        ], [
            'image.required' => 'الصورة مطلوبة',
            'image.image'    => 'الملف يجب أن يكون صورة',
            'url.required'   => 'الرابط مطلوب',
            'url.url'        => 'الرابط غير صالح',
        ]);

        $path = ImageService::uploadWebP($request->file('image'), 'sidebar-banners');

        SidebarBanner::create([
            'image'      => $path,
            'url'        => $request->url,
            'title'      => $request->title,
            'sort_order' => SidebarBanner::max('sort_order') + 1,
            'active'     => true,
        ]);

        return back()->with('success', 'تم إضافة البانر بنجاح');
    }

    public function update(Request $request, SidebarBanner $sidebarBanner)
    {
        $request->validate([
            'url'   => 'required|url|max:500',
            'title' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:4096',
        ]);

        $data = [
            'url'        => $request->url,
            'title'      => $request->title,
            'sort_order' => $request->sort_order ?? $sidebarBanner->sort_order,
            'active'     => $request->boolean('active'),
        ];

        if ($request->hasFile('image')) {
            ImageService::delete($sidebarBanner->image);
            $data['image'] = ImageService::uploadWebP($request->file('image'), 'sidebar-banners');
        }

        $sidebarBanner->update($data);

        return back()->with('success', 'تم تحديث البانر');
    }

    public function destroy(SidebarBanner $sidebarBanner)
    {
        ImageService::delete($sidebarBanner->image);
        $sidebarBanner->delete();
        return back()->with('success', 'تم حذف البانر');
    }
}
