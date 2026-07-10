@extends('layouts.app')

@section('title', 'دسته‌بندی‌ها')
@section('page-heading', 'مدیریت دسته‌بندی‌ها')

@section('content')
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h4 mb-1">دسته‌بندی‌ها</h1>
        <p class="text-muted mb-0">مدیریت دسته‌های محصولات و تصویر نمایشی هر دسته</p>
    </div>
    <a href="{{ route('categories.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg ms-1"></i>دسته جدید</a>
</div>

<div class="card">
    <div class="card-body border-bottom">
        <form class="row g-2">
            <div class="col-md-5">
                <input class="form-control" name="search" value="{{ request('search') }}" placeholder="جستجو در عنوان دسته‌بندی...">
            </div>
            <div class="col-auto"><button class="btn btn-outline-primary">جستجو</button></div>
            @if(request('search'))
                <div class="col-auto"><a class="btn btn-light" href="{{ route('categories.index') }}">پاک کردن</a></div>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>تصویر</th>
                    <th>عنوان دسته‌بندی</th>
                    <th>تعداد محصول</th>
                    <th>وضعیت</th>
                    <th>تاریخ ایجاد</th>
                    <th class="text-start">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>
                            @if($category->image)
                                <img class="thumb" src="{{ '/storage/'.ltrim($category->image, '/') }}" alt="{{ $category->name }}">
                            @else
                                <div class="thumb text-center pt-3"><i class="bi bi-image"></i></div>
                            @endif
                        </td>
                        <td class="fw-semibold">{{ $category->name }}</td>
                        <td>{{ $category->products_count }}</td>
                        <td><span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}-subtle text-{{ $category->is_active ? 'success' : 'secondary' }}">{{ $category->is_active ? 'فعال' : 'غیرفعال' }}</span></td>
                        <td>{{ $category->created_at->format('Y/m/d H:i') }}</td>
                        <td class="text-start">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('categories.edit', $category) }}"><i class="bi bi-pencil"></i></a>
                            <form class="d-inline" method="POST" action="{{ route('categories.destroy', $category) }}" data-confirm="دسته فقط در صورت نداشتن محصول حذف می‌شود.">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">دسته‌ای یافت نشد.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="card-footer bg-white pt-3">{{ $categories->links() }}</div>
    @endif
</div>
@endsection
