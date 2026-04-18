@extends('layouts.admin')
@section('title', 'روابط التحميل')

@section('content')
<style>
.dl-card { background:#fff; border:1px solid #dcdcde; border-radius:6px; margin-bottom:20px; overflow:hidden; }
.dl-card-header { background:#f6f7f7; border-bottom:1px solid #dcdcde; padding:10px 16px; display:flex; align-items:center; justify-content:space-between; gap:10px; }
.dl-post-title { font-weight:700; font-size:14px; color:#1d2327; text-decoration:none; }
.dl-post-title:hover { color:#2271b1; }
.dl-post-meta { font-size:11px; color:#646970; margin-top:2px; }
.dl-table { width:100%; border-collapse:collapse; font-size:13px; }
.dl-table th { background:#f6f7f7; color:#646970; font-weight:600; font-size:11px; text-transform:uppercase; padding:8px 14px; text-align:right; border-bottom:1px solid #dcdcde; }
.dl-table td { padding:10px 14px; border-bottom:1px solid #f0f0f1; vertical-align:middle; color:#1d2327; }
.dl-table tr:last-child td { border-bottom:none; }
.dl-table tr:hover td { background:#f9f9f9; }
.dl-badge { display:inline-block; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:600; }
.dl-badge-pc  { background:#dbeafe; color:#1e40af; }
.dl-badge-apk { background:#d1fae5; color:#065f46; }
.dl-badge-mac { background:#f3e8ff; color:#6b21a8; }
.dl-badge-ios { background:#fef3c7; color:#92400e; }
.dl-badge-all { background:#f3f4f6; color:#374151; }

/* Inline edit form */
.edit-row { display:none; background:#fffbeb; }
.edit-row.open { display:table-row; }
.edit-row td { padding:12px 14px; border-bottom:1px solid #fde68a; }
.edit-form-grid { display:grid; grid-template-columns:1fr 2fr 1fr 1fr 1fr; gap:8px; align-items:end; }
.edit-form-grid input { width:100%; padding:6px 10px; border:1px solid #dcdcde; border-radius:4px; font-size:13px; }
.edit-form-grid input:focus { outline:none; border-color:#2271b1; box-shadow:0 0 0 1px #2271b1; }
.btn-save  { background:#2271b1; color:#fff; border:none; padding:6px 14px; border-radius:4px; font-size:13px; cursor:pointer; font-weight:600; }
.btn-save:hover { background:#135e96; }
.btn-cancel { background:#f6f7f7; color:#50575e; border:1px solid #dcdcde; padding:6px 14px; border-radius:4px; font-size:13px; cursor:pointer; }
.btn-cancel:hover { background:#dcdcde; }
.btn-edit   { background:#f0f6fc; color:#2271b1; border:1px solid #c3d9f0; padding:4px 10px; border-radius:4px; font-size:12px; cursor:pointer; }
.btn-edit:hover { background:#2271b1; color:#fff; }
.btn-del    { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; padding:4px 10px; border-radius:4px; font-size:12px; cursor:pointer; }
.btn-del:hover { background:#dc2626; color:#fff; }

/* Add link form */
.add-link-section { background:#fff; border:1px solid #dcdcde; border-radius:6px; padding:18px 20px; margin-bottom:20px; }
.add-link-section h3 { font-size:14px; font-weight:700; color:#1d2327; margin:0 0 12px; }
.add-form-grid { display:grid; grid-template-columns:2fr 3fr 1fr 1fr 1fr; gap:10px; align-items:end; }
.add-form-grid label { font-size:12px; color:#50575e; font-weight:600; display:block; margin-bottom:3px; }
.add-form-grid input, .add-form-grid select { width:100%; padding:7px 10px; border:1px solid #dcdcde; border-radius:4px; font-size:13px; }
.post-select-wrap { grid-column: span 5; }
.post-select-wrap select { width:100%; padding:7px 10px; border:1px solid #dcdcde; border-radius:4px; font-size:13px; }

/* Search bar */
.search-bar { display:flex; gap:8px; margin-bottom:16px; }
.search-bar input { flex:1; padding:7px 12px; border:1px solid #dcdcde; border-radius:4px; font-size:13px; }
.search-bar button { background:#2271b1; color:#fff; border:none; padding:7px 16px; border-radius:4px; font-size:13px; cursor:pointer; }
.search-bar button:hover { background:#135e96; }

.empty-state { padding:24px; text-align:center; color:#646970; font-size:13px; }
</style>

{{-- Add New Link --}}
<div class="add-link-section" x-data="{ open: false }">
    <div class="flex items-center justify-between">
        <h3><i class="fas fa-plus-circle" style="color:#2271b1;margin-left:6px;"></i> إضافة رابط تحميل جديد</h3>
        <button @click="open=!open" class="btn-edit" style="font-size:13px;padding:5px 14px;">
            <span x-text="open ? 'إخفاء' : 'إضافة رابط'"></span>
        </button>
    </div>
    <div x-show="open" x-transition style="margin-top:14px;">
        <form action="{{ route('admin.download-links.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:10px;">
                <label style="font-size:12px;color:#50575e;font-weight:600;display:block;margin-bottom:3px;">
                    المقال — Post <span style="color:#dc2626;">*</span>
                </label>
                <select name="post_id" required style="width:100%;padding:7px 10px;border:1px solid #dcdcde;border-radius:4px;font-size:13px;">
                    <option value="">-- اختر مقالاً --</option>
                    @foreach($allPosts as $p)
                    <option value="{{ $p->id }}" {{ old('post_id') == $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="add-form-grid">
                <div>
                    <label>العنوان — Label <span style="color:#dc2626;">*</span></label>
                    <input type="text" name="label" value="{{ old('label', 'تحميل مباشر') }}" required placeholder="تحميل مباشر">
                </div>
                <div>
                    <label>رابط التحميل — URL <span style="color:#dc2626;">*</span></label>
                    <input type="url" name="url" value="{{ old('url') }}" required placeholder="https://...">
                </div>
                <div>
                    <label>حجم الملف — Size</label>
                    <input type="text" name="file_size" value="{{ old('file_size') }}" placeholder="e.g. 2.5 GB">
                </div>
                <div>
                    <label>الإصدار — Version</label>
                    <input type="text" name="version" value="{{ old('version') }}" placeholder="e.g. 1.0.0">
                </div>
                <div style="display:flex;align-items:flex-end;gap:6px;">
                    <button type="submit" class="btn-save" style="width:100%;">حفظ</button>
                </div>
            </div>
            @error('url')<p style="color:#dc2626;font-size:12px;margin-top:4px;">{{ $message }}</p>@enderror
        </form>
    </div>
</div>

{{-- Search --}}
<form method="GET" action="{{ route('admin.download-links.index') }}" class="search-bar">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم المقال...">
    <button type="submit"><i class="fas fa-search"></i> بحث</button>
    @if(request('search'))
    <a href="{{ route('admin.download-links.index') }}" style="background:#f6f7f7;color:#50575e;border:1px solid #dcdcde;padding:7px 14px;border-radius:4px;font-size:13px;text-decoration:none;">مسح</a>
    @endif
</form>

{{-- Posts with their links --}}
@forelse($posts as $post)
<div class="dl-card">
    <div class="dl-card-header">
        <div>
            <a href="{{ route('admin.posts.edit', $post) }}" class="dl-post-title">
                <i class="fas fa-file-alt" style="color:#646970;margin-left:5px;font-size:12px;"></i>
                {{ $post->title }}
            </a>
            <div class="dl-post-meta">
                {{ $post->downloadLinks->count() }} رابط
                · <a href="{{ url($post->slug) }}" target="_blank" style="color:#2271b1;">عرض المقال</a>
            </div>
        </div>
        <a href="{{ route('admin.posts.edit', $post) }}#download-links" class="btn-edit">
            <i class="fas fa-plus" style="font-size:10px;"></i> إضافة رابط للمقال
        </a>
    </div>

    <table class="dl-table">
        <thead>
            <tr>
                <th>العنوان — Label</th>
                <th>الرابط — URL</th>
                <th>الحجم — Size</th>
                <th>الإصدار — Version</th>
                <th>النقرات — Clicks</th>
                <th>الإجراءات — Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($post->downloadLinks as $link)
            {{-- View row --}}
            <tr id="row-{{ $link->id }}">
                <td><strong>{{ $link->label }}</strong></td>
                <td>
                    <a href="{{ $link->url }}" target="_blank" style="color:#2271b1;font-size:12px;word-break:break-all;">
                        {{ Str::limit($link->url, 50) }}
                    </a>
                </td>
                <td>{{ $link->file_size ?: '—' }}</td>
                <td>{{ $link->version ?: '—' }}</td>
                <td>
                    <span style="font-weight:700;color:#1d2327;">{{ number_format($link->clicks) }}</span>
                </td>
                <td style="white-space:nowrap;">
                    <button class="btn-edit" onclick="toggleEdit({{ $link->id }})">
                        <i class="fas fa-pencil-alt"></i> تعديل
                    </button>
                    <form method="POST" action="{{ route('admin.download-links.destroy', $link) }}"
                          style="display:inline-block;"
                          onsubmit="return confirm('حذف هذا الرابط نهائياً؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-del">
                            <i class="fas fa-trash"></i> حذف
                        </button>
                    </form>
                </td>
            </tr>
            {{-- Inline edit row --}}
            <tr class="edit-row" id="edit-{{ $link->id }}">
                <td colspan="6">
                    <form method="POST" action="{{ route('admin.download-links.update', $link) }}">
                        @csrf @method('PUT')
                        <div class="edit-form-grid">
                            <div>
                                <label style="font-size:11px;color:#50575e;font-weight:600;display:block;margin-bottom:3px;">العنوان</label>
                                <input type="text" name="label" value="{{ $link->label }}" required>
                            </div>
                            <div>
                                <label style="font-size:11px;color:#50575e;font-weight:600;display:block;margin-bottom:3px;">الرابط URL</label>
                                <input type="url" name="url" value="{{ $link->url }}" required>
                            </div>
                            <div>
                                <label style="font-size:11px;color:#50575e;font-weight:600;display:block;margin-bottom:3px;">الحجم</label>
                                <input type="text" name="file_size" value="{{ $link->file_size }}">
                            </div>
                            <div>
                                <label style="font-size:11px;color:#50575e;font-weight:600;display:block;margin-bottom:3px;">الإصدار</label>
                                <input type="text" name="version" value="{{ $link->version }}">
                            </div>
                            <div style="display:flex;gap:6px;align-items:flex-end;">
                                <button type="submit" class="btn-save">حفظ</button>
                                <button type="button" class="btn-cancel" onclick="toggleEdit({{ $link->id }})">إلغاء</button>
                            </div>
                        </div>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@empty
<div style="background:#fff;border:1px solid #dcdcde;border-radius:6px;">
    <div class="empty-state">
        <i class="fas fa-link" style="font-size:32px;color:#dcdcde;display:block;margin-bottom:8px;"></i>
        لا توجد روابط تحميل بعد
        @if(request('search'))
        — لا توجد نتائج لـ "{{ request('search') }}"
        @endif
    </div>
</div>
@endforelse

{{-- Pagination --}}
<div style="margin-top:16px;">
    {{ $posts->links() }}
</div>

@endsection

@section('scripts')
<script>
function toggleEdit(id) {
    const editRow = document.getElementById('edit-' + id);
    editRow.classList.toggle('open');
}
</script>
@endsection
