<?php

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->actingAs(Admin::factory()->create());
});

test('product code is generated per category and is never reused', function () {
    $category = Category::factory()->create(['code' => 'RNG']);
    $payload = [
        'title' => 'انگشتر اول', 'category_id' => $category->id,
        'availability' => Product::AVAILABLE, 'main_image' => UploadedFile::fake()->image('main.jpg'),
    ];

    $this->post(route('products.store'), $payload)->assertRedirect();
    expect(Product::first()->code)->toBe('RNG-000001');

    $this->delete(route('products.destroy', Product::first()))->assertRedirect();
    $payload['title'] = 'انگشتر دوم';
    $payload['main_image'] = UploadedFile::fake()->image('second.jpg');
    $this->post(route('products.store'), $payload)->assertRedirect();

    expect(Product::first()->code)->toBe('RNG-000002');
});

test('gallery accepts at most three images', function () {
    $category = Category::factory()->create();
    $gallery = collect(range(1, 4))->map(fn ($i) => UploadedFile::fake()->image("$i.jpg"))->all();

    $this->post(route('products.store'), [
        'title' => 'محصول', 'category_id' => $category->id,
        'availability' => Product::AVAILABLE, 'main_image' => UploadedFile::fake()->image('main.jpg'), 'gallery' => $gallery,
    ])->assertSessionHasErrors('gallery');
});

test('uploaded product images are resized and converted to webp', function () {
    $category = Category::factory()->create();

    $this->post(route('products.store'), [
        'title' => 'تصویر بهینه',
        'category_id' => $category->id,
        'availability' => Product::AVAILABLE,
        'main_image' => UploadedFile::fake()->image('large.jpg', 2400, 1800),
    ])->assertRedirect();

    $product = Product::firstOrFail();
    Storage::disk('public')->assertExists($product->main_image);
    expect($product->main_image)->toEndWith('.webp');

    [$width, $height, $type] = getimagesize(Storage::disk('public')->path($product->main_image));
    expect($width)->toBeLessThanOrEqual(1600)
        ->and($height)->toBeLessThanOrEqual(1600)
        ->and($type)->toBe(IMAGETYPE_WEBP);

    $this->get(route('products.show', $product))
        ->assertOk()
        ->assertSee('/storage/'.$product->main_image, false);
});

test('category with products cannot be deleted', function () {
    $category = Category::factory()->create();
    Product::factory()->for($category)->create();

    $this->delete(route('categories.destroy', $category))->assertSessionHasErrors('category');
    $this->assertDatabaseHas('categories', ['id' => $category->id]);
});

test('unused product fields do not exist in database', function () {
    expect(Schema::hasColumns('products', ['slug', 'description', 'wage_amount']))->toBeFalse();
});
