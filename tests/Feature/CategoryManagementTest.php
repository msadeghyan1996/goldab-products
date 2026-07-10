<?php

use App\Models\Admin;
use App\Models\Category;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    $this->actingAs(Admin::factory()->create());
});

test('category is created with only title and status', function () {
    $this->post(route('categories.store'), [
        'name' => 'دسته جدید',
        'is_active' => true,
    ])->assertRedirect(route('categories.index'));

    $category = Category::where('name', 'دسته جدید')->firstOrFail();

    expect($category->is_active)->toBeTrue()
        ->and($category->code)->toBe('CAT'.str_pad((string) $category->id, 4, '0', STR_PAD_LEFT));
});

test('removed category fields do not exist in database', function () {
    expect(Schema::hasColumns('categories', ['slug', 'image', 'description', 'parent_id', 'sort_order']))->toBeFalse();
});
