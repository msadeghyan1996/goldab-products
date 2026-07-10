@extends('layouts.storefront')

@section('title', 'درباره ما')
@section('description', 'آشنایی با ایران گلد، پیشینه حرفه‌ای در صنعت طلا آب‌شده و جواهر، آدرس فروشگاه و راه‌های ارتباطی.')
@section('image', asset('logo.jpg'))
@section('keywords', 'درباره ایران گلد, طلا آب‌شده, جواهر, آدرس ایران گلد, تماس ایران گلد')

@section('content')
<section class="about-hero">
    <div class="container">
        <div class="about-hero-inner">
            <div class="about-copy">
                <span class="about-eyebrow"><i></i> درباره ایران گلد</span>
                <h1>پیشینه‌ای حرفه‌ای در طلا آب‌شده و جواهر</h1>
                <p>
                    ایران گلد با تکیه بر تجربه عملی در بازار طلا، سال‌هاست در حوزه طلا آب‌شده، مصنوعات طلا و جواهر فعالیت می‌کند.
                    تمرکز ما بر اصالت کالا، شفافیت قیمت، دقت در وزن و اجرت، و ارائه محصولاتی است که هم از نظر کیفیت ساخت و هم از نظر ارزش خرید قابل اعتماد باشند.
                </p>
                <p>
                    در مسیر فعالیت حرفه‌ای، ارتباط مستقیم با مشتریان، پاسخ‌گویی دقیق و معرفی روشن جزئیات محصول برای ما اصل بوده است.
                    ایران گلد تلاش می‌کند انتخاب طلا و جواهر را به تجربه‌ای مطمئن، آگاهانه و ماندگار تبدیل کند.
                </p>
            </div>

            <div class="about-logo-card">
                <img src="{{ asset('logo.jpg') }}" alt="لوگوی ایران گلد">
                <strong>ایران گلد</strong>
                <span>اصالت در انتخاب، ظرافت در جزئیات</span>
            </div>
        </div>
    </div>
</section>

<section class="about-contact-section">
    <div class="container">
        <div class="about-section-head">
            <h2>راه‌های ارتباطی</h2>
            <p>برای دریافت مشاوره، استعلام محصول یا ثبت سفارش می‌توانید از مسیرهای زیر با ایران گلد در ارتباط باشید.</p>
        </div>

        <div class="about-contact-grid">
            <div class="about-contact-card about-address-card">
                <span><i class="bi bi-geo-alt"></i></span>
                <div>
                    <h3>آدرس</h3>
                    <p>تبریز، بازار، حیاط امیر، تیمچه امیر شمالی، ایران گلد</p>
                </div>
            </div>

            <a class="about-contact-card" href="tel:04133129393">
                <span><i class="bi bi-telephone"></i></span>
                <div>
                    <h3>شماره تماس</h3>
                    <p dir="ltr">{{ \App\Support\PersianNumber::convert('04133129393') }}</p>
                </div>
            </a>

            <a class="about-contact-card" href="https://t.me/irgold24" target="_blank" rel="noopener">
                <span><i class="bi bi-telegram"></i></span>
                <div>
                    <h3>تلگرام</h3>
                    <p dir="ltr">{{ \App\Support\PersianNumber::convert('@irgold24') }}</p>
                </div>
            </a>

            <a class="about-contact-card" href="https://instagram.com/irgold24.ir" target="_blank" rel="noopener">
                <span><i class="bi bi-instagram"></i></span>
                <div>
                    <h3>اینستاگرام</h3>
                    <p dir="ltr">{{ \App\Support\PersianNumber::convert('irgold24.ir') }}</p>
                </div>
            </a>
        </div>
    </div>
</section>
@endsection
