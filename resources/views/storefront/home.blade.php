@extends('layouts.storefront')

@section('title', 'صفحه اصلی فروشگاه')

@section('content')
<section class="brand-hero-slider random-product-hero">
    @if($heroProducts->isNotEmpty())
        <div id="heroProductCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5200">
            <div class="carousel-inner">
                @foreach($heroProducts as $heroProduct)
                    <div class="carousel-item @if($loop->first) active @endif">
                        <div class="container">
                            <div class="hero-product-slide">
                                <div class="hero-product-copy">
                                    <span class="hero-product-eyebrow"><i></i>{{ $heroProduct->category->name }}</span>
                                    <h1>{{ $heroProduct->title }}</h1>

                                    <div class="hero-product-specs compact">
                                        <div>
                                            <span>وزن</span>
                                            <strong>{{ $heroProduct->weight !== null ? \App\Support\PersianNumber::convert(rtrim(rtrim(number_format((float) $heroProduct->weight, 3, '.', ''), '0'), '.')).' گرم' : 'نامشخص' }}</strong>
                                        </div>
                                        <div>
                                            <span>اجرت</span>
                                            <strong>{{ $heroProduct->wage_percentage !== null ? \App\Support\PersianNumber::convert(rtrim(rtrim($heroProduct->wage_percentage, '0'), '.')).'٪' : '—' }}</strong>
                                        </div>
                                    </div>

                                    <div class="hero-product-actions">
                                        <a class="slider-cta" href="{{ route('storefront.products.show', $heroProduct->id) }}">مشاهده جزئیات <i class="bi bi-arrow-left"></i></a>
                                    </div>
                                </div>

                                <a class="hero-product-visual" href="{{ route('storefront.products.show', $heroProduct->id) }}">
                                    <span class="hero-product-frame">
                                        @if($heroProduct->main_image)
                                            <img src="{{ '/storage/'.ltrim($heroProduct->main_image, '/') }}" alt="{{ $heroProduct->title }}">
                                        @else
                                            <span class="hero-product-placeholder"><i class="bi bi-gem"></i></span>
                                        @endif
                                    </span>
                                    <span class="hero-product-code">IRAN GOLD · {{ \App\Support\PersianNumber::convert($heroProduct->code) }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($heroProducts->count() > 1)
                <div class="carousel-indicators luxury-indicators hero-product-indicators">
                    @foreach($heroProducts as $heroProduct)
                        <button type="button" data-bs-target="#heroProductCarousel" data-bs-slide-to="{{ $loop->index }}" class="@if($loop->first) active @endif" aria-current="{{ $loop->first ? 'true' : 'false' }}" aria-label="محصول {{ \App\Support\PersianNumber::convert($loop->iteration) }}"></button>
                    @endforeach
                </div>
                <button class="carousel-control-prev luxury-control" type="button" data-bs-target="#heroProductCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">قبلی</span>
                </button>
                <button class="carousel-control-next luxury-control" type="button" data-bs-target="#heroProductCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">بعدی</span>
                </button>
            @endif
        </div>
    @else
        <div class="container">
            <div class="hero-product-empty">
                <img src="{{ asset('logo.jpg') }}" alt="ایران گلد">
                <h1>ویترین ایران گلد</h1>
                <p>به‌زودی محصولات منتخب در این بخش نمایش داده می‌شوند.</p>
            </div>
        </div>
    @endif
</section>

<section class="brand-pillars">
    <div class="container">
        <div class="row g-0">
            <div class="col-md-4 pillar"><span>۰۱</span><div><b>طلای اصیل</b><small>انتخابی مطمئن و ماندگار</small></div></div>
            <div class="col-md-4 pillar"><span>۰۲</span><div><b>قیمت شفاف</b><small>همگام با نرخ لحظه‌ای بازار</small></div></div>
            <div class="col-md-4 pillar"><span>۰۳</span><div><b>طراحی ظریف</b><small>زیبایی در دقیق‌ترین جزئیات</small></div></div>
        </div>
    </div>
</section>

<section class="editorial-products category-showcase home-latest-showcase">
    <div class="container">
        <div class="editorial-head category-editorial-head">
            <div class="editorial-title">
                <div>
                    <h2 class="latest-section-title">جدیدترین محصولات</h2>
                </div>
            </div>
            <a class="editorial-link" href="{{ route('storefront.catalog', ['all' => 1, 'section' => 'new']) }}">مشاهده همه <i class="bi bi-arrow-left"></i></a>
        </div>

        <div class="latest-category-row">
            <div class="latest-category-title">
                <span>دسته‌بندی محصولات</span>
                <small>انتخاب سریع‌تر از ویترین ایران گلد</small>
            </div>
            <div class="home-category-circles">
                @forelse($categories as $category)
                    <a class="home-category-circle" href="{{ route('storefront.catalog', ['all' => 1, 'category_id' => $category->id]) }}">
                        <span>
                            @if($category->image)
                                <img src="{{ '/storage/'.ltrim($category->image, '/') }}" alt="{{ $category->name }}">
                            @else
                                <i class="bi bi-gem"></i>
                            @endif
                        </span>
                        <strong>{{ $category->name }}</strong>
                    </a>
                @empty
                    <div class="empty-products py-3">هنوز دسته‌بندی دارای محصول ثبت نشده است.</div>
                @endforelse
            </div>
        </div>

        <div class="row g-3 g-lg-4">
            @include('storefront.partials.product-cards', ['products' => $newProducts])
        </div>

        <div class="latest-products-footer">
            <a href="{{ route('storefront.catalog', ['all' => 1]) }}">مشاهده همه محصولات <i class="bi bi-arrow-left"></i></a>
        </div>
    </div>
</section>

<section class="brand-closing">
    <div class="container">
        <div class="closing-luxury-frame">
            <div class="closing-luxury-copy">
                <h2>طلا فقط زینت نیست؛ روایت ارزش، اصالت و انتخابی ماندگار است.</h2>
            </div>
            <div class="closing-luxury-visual">
                <img src="{{ asset('lucimage.png') }}" alt="نمای لوکس طلا و جواهر ایران گلد">
            </div>
        </div>
    </div>
</section>
@endsection
