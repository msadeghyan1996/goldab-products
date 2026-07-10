<?php

use App\Models\Category;
use App\Models\GoldPrice;
use App\Models\Product;
use App\Support\PersianNumber;

test('main address displays random product hero latest products and product links by id', function () {
    $category = Category::factory()->create(['is_active' => true]);
    GoldPrice::create(['provider' => 'irangold', 'sell_price' => 17_255_600, 'fetched_at' => now()]);
    $product = Product::factory()->for($category)->create([
        'title' => 'انگشتر طلای تست',
        'weight' => 2.5,
        'wage_percentage' => 10,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('random-product-hero', false)
        ->assertSee('heroProductCarousel', false)
        ->assertSee($product->title)
        ->assertSee('مشاهده جزئیات')
        ->assertSee(route('storefront.live-prices'), false)
        ->assertSee('جدیدترین محصولات')
        ->assertSee('دسته‌بندی محصولات')
        ->assertDontSee('امضای ظرافت')
        ->assertDontSee('محصولات زیر ۳ گرم')
        ->assertDontSee('ظرافت زیر سه گرم')
        ->assertSee(PersianNumber::convert('1,095,455'))
        ->assertSee(route('storefront.products.show', $product->id), false);
});

test('old shop address permanently redirects to main address', function () {
    $this->get('/shop')->assertRedirect('/')->assertStatus(301);
});

test('public product detail uses a short id based url', function () {
    GoldPrice::create(['provider' => 'irangold', 'sell_price' => 17_255_600, 'fetched_at' => now()]);
    $product = Product::factory()->for(Category::factory()->state(['is_active' => true]))->create([
        'weight' => 2,
        'wage_percentage' => 10,
    ]);
    $related = Product::factory()->for($product->category)->create(['title' => 'محصول مشابه تست']);

    $this->get('/p/'.$product->id)
        ->assertOk()
        ->assertSee($product->title)
        ->assertSee('محصولات مشابه')
        ->assertSee($related->title)
        ->assertSee(PersianNumber::convert('876,364').' تومان')
        ->assertSee('قیمت لحظه‌ای محصول')
        ->assertSee('همگام با نرخ بازار')
        ->assertSee('نحوه محاسبه قیمت')
        ->assertSee('ابتدا وزن طلا در نرخ روز ضرب می‌شود')
        ->assertDontSee('نرخ لحظه‌ای هر گرم طلای ۱۸ عیار')
        ->assertDontSee('موجود')
        ->assertDontSee('ناموجود')
        ->assertDontSee('نرخ فروش لحظه‌ای مثقال طلا');
});

test('light products catalog only includes products under three grams', function () {
    $category = Category::factory()->create(['is_active' => true]);
    $light = Product::factory()->for($category)->create(['title' => 'محصول سبک', 'weight' => 2.999]);
    Product::factory()->for($category)->create(['title' => 'محصول سنگین', 'weight' => 3]);

    $this->get(route('storefront.catalog', ['all' => 1, 'section' => 'light']))
        ->assertOk()
        ->assertSee($light->title)
        ->assertDontSee('محصول سنگین');
});

test('catalog endpoint returns paginated html for infinite loading', function () {
    $category = Category::factory()->create(['is_active' => true]);
    Product::factory()->count(3)->for($category)->create();

    $this->getJson(route('storefront.catalog.load', ['per_page' => 2]))
        ->assertOk()
        ->assertJsonStructure(['html', 'next_page_url'])
        ->assertJsonPath('next_page_url', fn ($url) => str_contains($url, 'page=2'));
});

test('live price endpoint returns current gram and product prices', function () {
    GoldPrice::create(['provider' => 'irangold', 'sell_price' => 17_255_600, 'fetched_at' => now()]);
    $product = Product::factory()->for(Category::factory()->state(['is_active' => true]))->create([
        'weight' => 2,
        'wage_percentage' => 10,
    ]);

    $this->getJson(route('storefront.live-prices', ['product_ids' => [$product->id]]))
        ->assertOk()
        ->assertJsonPath('rate.gram_price', 399_000)
        ->assertJsonPath('products.'.$product->id, 876_364);
});
