@extends('layouts.storefront')

@section('title', $product->title)
@section('description', $product->short_description ?: $product->title)

@section('content')
<div class="container product-detail py-4 py-lg-5">
    <nav class="small mb-4">
        <a href="{{ route('storefront.catalog') }}">خانه</a><span class="mx-2">/</span>
        <a href="{{ route('storefront.catalog', ['all' => 1, 'category_id' => $product->category_id]) }}">{{ $product->category->name }}</a>
        <span class="mx-2">/</span><span>{{ $product->title }}</span>
    </nav>

    <div class="row g-4 g-lg-5">
        <div class="col-lg-6 order-2 order-lg-1">
            <div class="detail-main-image">
                @if($product->main_image)
                    <img src="{{ '/storage/'.ltrim($product->main_image, '/') }}" alt="{{ $product->title }}">
                @else
                    <div class="product-placeholder"><i class="bi bi-image"></i></div>
                @endif
            </div>
            @if($product->images->isNotEmpty())
                <div class="detail-gallery mt-3">
                    @foreach($product->images as $image)
                        <img src="{{ '/storage/'.ltrim($image->path, '/') }}" alt="{{ $product->title }}" loading="lazy">
                    @endforeach
                </div>
            @endif
        </div>

        <div class="col-lg-6 order-1 order-lg-2">
            <div class="detail-info">
                <span class="detail-category">{{ $product->category->name }}</span>
                <h1>{{ $product->title }}</h1>
                <div class="detail-code">کد محصول: <b>{{ \App\Support\PersianNumber::convert($product->code) }}</b></div>

                <div class="product-price-row live-product-price detail-top-price">
                    <div class="live-price-heading">
                        <span>قیمت لحظه‌ای محصول</span>
                        @if($goldRate)
                            <small><i class="live-mini-dot {{ $goldRate['is_live'] ? '' : 'is-stale' }}"></i>{{ $goldRate['is_live'] ? 'همگام با نرخ بازار' : 'بر اساس آخرین نرخ ثبت‌شده' }}</small>
                        @endif
                        <button class="price-help-link" type="button" data-bs-toggle="modal" data-bs-target="#pricingGuideModal">
                            <i class="bi bi-exclamation-circle"></i>
                            نحوه محاسبه قیمت
                        </button>
                    </div>
                    <strong data-live-product-id="{{ $product->id }}">{{ $productPrice !== null ? \App\Support\PersianNumber::convert(number_format($productPrice)).' تومان' : 'نیازمند وزن و نرخ طلا' }}</strong>
                </div>

                <div class="spec-list">
                    <div><span>وزن</span><strong>{{ $product->weight !== null ? \App\Support\PersianNumber::convert(rtrim(rtrim(number_format((float) $product->weight, 3, '.', ''), '0'), '.')).' گرم' : '—' }}</strong></div>
                    <div><span>اجرت درصدی</span><strong>{{ $product->wage_percentage !== null ? \App\Support\PersianNumber::convert(rtrim(rtrim($product->wage_percentage, '0'), '.')).'٪' : '—' }}</strong></div>
                </div>

                @if($product->short_description)
                    <div class="detail-description"><h2>توضیحات محصول</h2><p>{{ $product->short_description }}</p></div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade pricing-guide-modal" id="pricingGuideModal" tabindex="-1" aria-labelledby="pricingGuideModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="pricing-guide-title">
                        <span><i class="bi bi-calculator"></i></span>
                        <div>
                            <small>شفافیت قیمت</small>
                            <h2 class="modal-title" id="pricingGuideModalLabel">نحوه محاسبه قیمت</h2>
                        </div>
                    </div>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>
                <div class="modal-body">
                    <p class="pricing-guide-lead">قیمت نهایی بر اساس وزن محصول، نرخ روز هر گرم طلا و اجرت ساخت محاسبه می‌شود.</p>

                    <div class="pricing-formula">
                        <span>فرمول محاسبه</span>
                        <strong>وزن × نرخ هر گرم × (۱ + اجرت ÷ ۱۰۰)</strong>
                    </div>

                    <div class="pricing-steps" aria-label="مراحل محاسبه قیمت">
                        <div>
                            <span>۱</span>
                            <p>نرخ مثقال بازار به نرخ هر گرم طلا تبدیل می‌شود.</p>
                        </div>
                        <div>
                            <span>۲</span>
                            <p>نرخ هر گرم در وزن دقیق محصول ضرب می‌شود.</p>
                        </div>
                        <div>
                            <span>۳</span>
                            <p>اجرت درصدی محصول به مبلغ پایه اضافه می‌شود.</p>
                        </div>
                    </div>

                    <div class="pricing-guide-note">
                        <i class="bi bi-info-circle"></i>
                        <p>با تغییر نرخ بازار، قیمت محصول به‌صورت لحظه‌ای به‌روزرسانی می‌شود.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($relatedProducts->isNotEmpty())
        <section class="related-products-section">
            <div class="editorial-head category-editorial-head">
                <div class="editorial-title">
                    <div>
                        <h2>محصولات مشابه</h2>
                    </div>
                </div>
                <a class="editorial-link" href="{{ route('storefront.catalog', ['all' => 1, 'category_id' => $product->category_id]) }}">مشاهده همه <i class="bi bi-arrow-left"></i></a>
            </div>

            <div class="row g-3 g-lg-4">
                @include('storefront.partials.product-cards', ['products' => $relatedProducts])
            </div>
        </section>
    @endif
</div>
@endsection
