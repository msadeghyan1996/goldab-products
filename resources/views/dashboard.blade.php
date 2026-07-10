@extends('layouts.app')
@section('title', 'داشبورد')
@section('page-heading', 'داشبورد')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="h4 mb-1">داشبورد</h1><p class="text-muted mb-0">نمای کلی فروشگاه</p></div><a href="{{ route('products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg ms-1"></i>محصول جدید</a></div>
<div class="row g-3 mb-4">
    @foreach ([['products','کل محصولات','box-seam','primary'],['categories','دسته‌بندی‌ها','diagram-3','info'],['admins','مدیران','people','secondary'],['available','محصول موجود','check-circle','success'],['unavailable','محصول ناموجود','x-circle','danger']] as [$key,$label,$icon,$color])
    <div class="col-6 col-lg"><div class="card stat-card h-100"><div class="card-body d-flex align-items-center gap-3"><div class="icon bg-{{ $color }}-subtle text-{{ $color }}"><i class="bi bi-{{ $icon }}"></i></div><div><div class="h4 mb-0">{{ number_format($stats[$key]) }}</div><small class="text-muted">{{ $label }}</small></div></div></div></div>
    @endforeach
</div>
<div class="card"><div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between"><h2 class="h6">آخرین محصولات ثبت‌شده</h2><a href="{{ route('products.index') }}" class="small text-decoration-none">مشاهده همه</a></div><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>محصول</th><th>کد</th><th>دسته</th><th>وضعیت</th><th>تاریخ</th></tr></thead><tbody>
@forelse($latestProducts as $product)<tr><td><a class="text-decoration-none text-dark fw-semibold" href="{{ route('products.show',$product) }}">{{ $product->title }}</a></td><td dir="ltr" class="text-end">{{ $product->code }}</td><td>{{ $product->category->name }}</td><td><span class="badge bg-{{ $product->availability === 'available' ? 'success' : 'secondary' }}-subtle text-{{ $product->availability === 'available' ? 'success' : 'secondary' }}">{{ $product->availability === 'available' ? 'موجود' : 'ناموجود' }}</span></td><td>{{ $product->created_at->format('Y/m/d') }}</td></tr>
@empty<tr><td colspan="5" class="text-center py-5 text-muted">محصولی ثبت نشده است.</td></tr>@endforelse
</tbody></table></div></div>
@endsection
