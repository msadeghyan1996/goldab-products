<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $siteName = config('app.name', 'ایران گلد');
        $pageTitle = trim($__env->yieldContent('title', 'فروشگاه'));
        $fullTitle = $pageTitle ? $pageTitle.' | '.$siteName : $siteName;
        $seoDescription = trim(strip_tags($__env->yieldContent('description', 'ایران گلد؛ مرجع مشاهده و انتخاب طلا و جواهر با طراحی ظریف، قیمت شفاف و محصولات به‌روز.')));
        $seoKeywords = trim(strip_tags($__env->yieldContent('keywords', 'ایران گلد, طلا, جواهر, خرید طلا, قیمت طلا, فروشگاه طلا')));
        $seoImage = trim($__env->yieldContent('image', asset('logo.jpg')));
        $seoUrl = trim($__env->yieldContent('canonical', url()->current()));
        $seoType = trim($__env->yieldContent('type', 'website'));
    @endphp
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="{{ $seoKeywords }}">
    <meta name="robots" content="@yield('robots', 'index, follow, max-image-preview:large')">
    <meta name="author" content="{{ $siteName }}">
    <meta name="theme-color" content="#061426">
    <link rel="canonical" href="{{ $seoUrl }}">
    <link rel="icon" href="{{ asset('logo.jpg') }}" type="image/jpeg">
    <link rel="apple-touch-icon" href="{{ asset('logo.jpg') }}">
    <meta property="og:locale" content="fa_IR">
    <meta property="og:type" content="{{ $seoType }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $seoUrl }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:image:secure_url" content="{{ $seoImage }}">
    <meta property="og:image:alt" content="{{ $siteName }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $fullTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/storefront.css') }}?v={{ filemtime(public_path('css/storefront.css')) }}" rel="stylesheet">
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => url('/'),
            'logo' => asset('logo.jpg'),
            'sameAs' => [
                'https://instagram.com/irgold24.ir',
                'https://t.me/irgold24',
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '+984133129393',
                'contactType' => 'customer service',
                'areaServed' => 'IR',
                'availableLanguage' => ['fa'],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'url' => url('/'),
            'inLanguage' => 'fa-IR',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    @stack('structured_data')
    @stack('head')
</head>
<body data-live-prices-endpoint="{{ route('storefront.live-prices') }}">
<header class="store-header sticky-top">
    <nav class="navbar navbar-expand-lg container py-3">
        <a class="navbar-brand" href="{{ route('storefront.catalog') }}">
            <img class="brand-logo" src="{{ asset('logo.jpg') }}" alt="لوگوی ایران گلد">
            <span class="brand-copy"><strong>ایران گلد</strong><small>نماد اعتماد در صنعت طلا</small></span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#storefrontNavbar" aria-controls="storefrontNavbar" aria-expanded="false" aria-label="نمایش منو">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="storefrontNavbar">
            <ul class="navbar-nav me-lg-4 align-items-lg-center gap-lg-2">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('storefront.catalog') && ! request()->boolean('all') ? 'active' : '' }}" href="{{ route('storefront.catalog') }}">صفحه اصلی</a>
                </li>
                <li class="nav-item dropdown storefront-category-dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->filled('category_id') ? 'active' : '' }}" href="{{ route('storefront.catalog', ['all' => 1]) }}" id="categoryMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        دسته‌بندی
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end luxury-dropdown" aria-labelledby="categoryMenu">
                        <li><a class="dropdown-item" href="{{ route('storefront.catalog', ['all' => 1]) }}">همه محصولات</a></li>
                        @forelse($headerCategories as $headerCategory)
                            <li><a class="dropdown-item" href="{{ route('storefront.catalog', ['all' => 1, 'category_id' => $headerCategory->id]) }}">{{ $headerCategory->name }}</a></li>
                        @empty
                            <li><span class="dropdown-item-text">دسته‌بندی فعالی وجود ندارد</span></li>
                        @endforelse
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('storefront.about') ? 'active' : '' }}" href="{{ route('storefront.about') }}">درباره ما</a>
                </li>
            </ul>
        </div>

        <div class="store-header-rate">
            <span class="live-label"><i></i> نرخ لحظه‌ای</span>
            @if($headerGoldRate)
                <strong><span data-live-gold-rate>{{ \App\Support\PersianNumber::convert(number_format($headerGoldRate['gram_price'])) }}</span> <small>تومان</small></strong>
            @else
                <strong><span data-live-gold-rate>—</span></strong>
            @endif
        </div>
    </nav>
</header>

<main>@yield('content')</main>

<footer id="about" class="store-footer mt-5">
    <div class="container py-5">
        <div class="footer-grid">
            <div class="footer-brand-block">
                <div class="footer-brand">
                    <img src="{{ asset('logo.jpg') }}" alt="لوگوی ایران گلد">
                    <span><b>ایران گلد</b><small>اصالت در انتخاب، ظرافت در جزئیات</small></span>
                </div>
                <p>ایران گلد، مرجع انتخاب طلا و جواهر با تمرکز بر اصالت، شفافیت قیمت و ظرافت در طراحی.</p>
            </div>

            <div class="footer-contact">
                <h3>اطلاعات تماس</h3>
                <ul>
                    <li><i class="bi bi-geo-alt"></i><span>تبریز-بازار-حیاط امیر-تیمچه امیر شمالی - ایران گلد</span></li>
                    <li><i class="bi bi-telephone"></i><a href="tel:04133129393" dir="ltr">{{ \App\Support\PersianNumber::convert('04133129393') }}</a></li>
                    <li><i class="bi bi-instagram"></i><a href="https://instagram.com/irgold24.ir" target="_blank" rel="noopener">{{ \App\Support\PersianNumber::convert('irgold24.ir') }}</a></li>
                    <li><i class="bi bi-telegram"></i><a href="https://t.me/irgold24" target="_blank" rel="noopener">{{ \App\Support\PersianNumber::convert('irgold24') }}</a></li>
                </ul>
            </div>

            <div class="footer-actions">
                <h3>دسترسی سریع</h3>
                <a href="{{ route('storefront.catalog') }}">صفحه اصلی</a>
                <a href="{{ route('storefront.catalog', ['all' => 1]) }}">مشاهده محصولات</a>
                @foreach($footerCategories as $footerCategory)
                    <a href="{{ route('storefront.catalog', ['all' => 1, 'category_id' => $footerCategory->id]) }}">{{ $footerCategory->name }}</a>
                @endforeach
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/storefront.js') }}"></script>
@stack('scripts')
</body>
</html>
