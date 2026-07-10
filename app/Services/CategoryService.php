<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    public function __construct(private readonly ImageOptimizer $images) {}

    public function create(array $data): Category
    {
        if (isset($data['image'])) {
            $data['image'] = $this->images->store($data['image'], 'categories', 900, 700);
        }

        $category = Category::create([
            ...$data,
            'code' => 'TMP'.Str::upper(Str::random(12)),
        ]);

        $category->forceFill([
            'code' => 'CAT'.str_pad((string) $category->id, 4, '0', STR_PAD_LEFT),
        ])->save();

        return $category;
    }

    public function update(Category $category, array $data): Category
    {
        if (isset($data['image'])) {
            $this->deleteImage($category->image);
            $data['image'] = $this->images->store($data['image'], 'categories', 900, 700);
        }

        $category->update($data);

        return $category;
    }

    public function delete(Category $category): void
    {
        if ($category->products()->exists()) {
            throw ValidationException::withMessages(['category' => 'دسته دارای محصول قابل حذف نیست.']);
        }

        $image = $category->image;
        $category->delete();
        $this->deleteImage($image);
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
