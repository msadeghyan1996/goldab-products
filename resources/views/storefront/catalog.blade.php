@extends('layouts.storefront')
@section('title', $title)
@section('content')
<section class="catalog-hero"><div class="container"><h1>{{ $title }}</h1><p>برای مشاهده جزئیات، هر محصول را انتخاب کنید.</p></div></section>
<div class="container py-5">
    @if($section === 'category')
    <div class="category-filters mb-4">
        <a class="category-badge {{ $categoryId ? '' : 'active' }}" href="{{ route('storefront.catalog', ['all' => 1]) }}">همه</a>
        @foreach($categories as $category)<a class="category-badge {{ (int) $categoryId === $category->id ? 'active' : '' }}" href="{{ route('storefront.catalog', ['all' => 1, 'category_id' => $category->id]) }}">{{ $category->name }}</a>@endforeach
    </div>
    @endif
    <div class="row g-3 g-lg-4" data-infinite-products>@include('storefront.partials.product-cards', ['products' => $products])</div>
    <div class="infinite-status" data-infinite-loader data-next-url="{{ $nextPageUrl }}">
        @if($nextPageUrl)<span class="spinner-border spinner-border-sm"></span><span>در حال دریافت محصولات بیشتر...</span>@else<span>همه محصولات نمایش داده شدند.</span>@endif
    </div>
</div>
@endsection
