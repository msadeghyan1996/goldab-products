@forelse($products as $product)
<div class="col-6 col-md-4 col-xl-3 product-card-column">
    <a class="product-card card h-100 text-decoration-none" href="{{ route('storefront.products.show', $product->id) }}">
        <div class="product-image-wrap">
            @if($product->main_image)
                <img src="{{ '/storage/'.ltrim($product->main_image, '/') }}" alt="{{ $product->title }}" loading="lazy">
            @else
                <div class="product-placeholder"><i class="bi bi-image"></i></div>
            @endif
            <span class="availability-dot {{ $product->availability === 'available' ? 'available' : '' }}"></span>
        </div>
        <div class="card-body p-3">
            <small class="product-category">{{ $product->category->name }}</small>
            <h3 class="product-title">{{ $product->title }}</h3>
            <div class="product-meta">
                <span><i class="bi bi-speedometer2"></i> {{ $product->weight !== null ? \App\Support\PersianNumber::convert(rtrim(rtrim(number_format((float) $product->weight, 3, '.', ''), '0'), '.')).' گرم' : 'وزن نامشخص' }}</span>
                @if($product->wage_percentage !== null)<span>{{ \App\Support\PersianNumber::convert(rtrim(rtrim($product->wage_percentage, '0'), '.')) }}٪ اجرت</span>@endif
            </div>
            <div class="product-card-price">
                @if($product->calculated_price !== null)
                    <small><i></i> قیمت لحظه‌ای</small>
                    <div><strong data-live-product-id="{{ $product->id }}">{{ \App\Support\PersianNumber::convert(number_format($product->calculated_price)) }}</strong><span>تومان</span></div>
                @else
                    <span>برای قیمت تماس بگیرید</span>
                @endif
            </div>
        </div>
    </a>
</div>
@empty
<div class="col-12 empty-products"><i class="bi bi-box-seam"></i><p>محصولی برای نمایش وجود ندارد.</p></div>
@endforelse
