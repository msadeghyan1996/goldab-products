@extends('layouts.storefront')
@section('title', $title)
@section('description', $title.' ایران گلد؛ مشاهده محصولات طلا و جواهر با اطلاعات کامل، تصاویر محصول و قیمت به‌روز.')
@section('image', asset('logo.jpg'))
@section('keywords', 'محصولات طلا, جواهر, ایران گلد, خرید طلا, دسته‌بندی طلا')
@section('content')
<div class="container py-5">
    @if($section === 'category')
    <div class="category-filters mb-4">
        <a class="category-badge {{ $categoryId ? '' : 'active' }}" href="{{ route('storefront.catalog', ['all' => 1]) }}">همه</a>
        @foreach($categories as $category)<a class="category-badge {{ (int) $categoryId === $category->id ? 'active' : '' }}" href="{{ route('storefront.catalog', ['all' => 1, 'category_id' => $category->id]) }}">{{ $category->name }}</a>@endforeach
    </div>
    @endif
    <div class="row g-3 g-lg-4" data-infinite-products>@include('storefront.partials.product-cards', ['products' => $products])</div>
    <div class="infinite-status" data-infinite-loader data-next-url="{{ $nextPageUrl }}">
        @if($nextPageUrl)<span class="spinner-border spinner-border-sm"></span><span>در حال دریافت محصولات بیشتر...</span>@endif
    </div>
</div>
@endsection
