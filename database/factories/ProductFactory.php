<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);
        $sequence = fake()->unique()->numberBetween(1, 999999999);

        return [
            'category_id' => Category::factory(),
            'title' => $title,
            'code' => 'TST-'.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT),
            'code_sequence' => $sequence,
            'short_description' => fake()->sentence(),
            'weight' => fake()->randomFloat(3, 0.1, 1000),
            'weight_unit' => 'گرم',
            'wage_percentage' => fake()->randomFloat(2, 0, 100),
            'availability' => fake()->randomElement([Product::AVAILABLE, Product::UNAVAILABLE]),
        ];
    }
}
