<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProductService
{
    public function __construct(private readonly ImageOptimizer $images) {}

    public function create(array $data, UploadedFile $mainImage, array $gallery): Product
    {
        $newPaths = [];

        try {
            $data['main_image'] = $this->store(
                $mainImage,
                'products/main',
                config('images.product_main.width'),
                config('images.product_main.height'),
                $newPaths,
            );
            $galleryPaths = array_map(fn (UploadedFile $file) => $this->store(
                $file,
                'products/gallery',
                config('images.product_gallery.width'),
                config('images.product_gallery.height'),
                $newPaths,
            ), $gallery);

            return DB::transaction(function () use ($data, $galleryPaths): Product {
                $category = Category::query()->lockForUpdate()->findOrFail($data['category_id']);
                [$code, $sequence] = $this->nextCode($category);

                $product = new Product(Arr::except($data, ['gallery', 'remove_gallery', 'remove_main_image']));
                $product->code = $code;
                $product->code_sequence = $sequence;
                $product->save();

                foreach ($galleryPaths as $position => $path) {
                    $product->images()->create(['path' => $path, 'sort_order' => $position]);
                }

                return $product->load(['category', 'images']);
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($newPaths);
            throw $exception;
        }
    }

    public function update(Product $product, array $data, ?UploadedFile $mainImage, array $gallery): Product
    {
        $newPaths = [];
        $oldPaths = [];

        try {
            if ($mainImage) {
                $data['main_image'] = $this->store(
                    $mainImage,
                    'products/main',
                    config('images.product_main.width'),
                    config('images.product_main.height'),
                    $newPaths,
                );
                if ($product->main_image) {
                    $oldPaths[] = $product->main_image;
                }
            } elseif (! empty($data['remove_main_image']) && $product->main_image) {
                $oldPaths[] = $product->main_image;
                $data['main_image'] = null;
            }

            $galleryPaths = array_map(fn (UploadedFile $file) => $this->store(
                $file,
                'products/gallery',
                config('images.product_gallery.width'),
                config('images.product_gallery.height'),
                $newPaths,
            ), $gallery);

            DB::transaction(function () use ($product, $data, $galleryPaths, &$oldPaths): void {
                if ((int) $data['category_id'] !== $product->category_id) {
                    $category = Category::query()->lockForUpdate()->findOrFail($data['category_id']);
                    [$product->code, $product->code_sequence] = $this->nextCode($category);
                }

                $removeIds = $data['remove_gallery'] ?? [];
                $removed = $product->images()->whereIn('id', $removeIds)->get();
                array_push($oldPaths, ...$removed->pluck('path')->all());
                $product->images()->whereIn('id', $removeIds)->delete();

                $product->fill(Arr::except($data, ['gallery', 'remove_gallery', 'remove_main_image']));
                $product->save();

                $nextPosition = (int) $product->images()->max('sort_order') + 1;
                foreach ($galleryPaths as $path) {
                    $product->images()->create(['path' => $path, 'sort_order' => $nextPosition++]);
                }
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($newPaths);
            throw $exception;
        }

        Storage::disk('public')->delete($oldPaths);

        return $product->refresh()->load(['category', 'images']);
    }

    public function delete(Product $product): void
    {
        $paths = $product->images()->pluck('path')->all();
        if ($product->main_image) {
            $paths[] = $product->main_image;
        }

        $product->delete();
        Storage::disk('public')->delete($paths);
    }

    private function nextCode(Category $category): array
    {
        $sequence = $category->next_product_sequence;
        $category->increment('next_product_sequence');

        return [sprintf('%s-%06d', strtoupper($category->code), $sequence), $sequence];
    }

    private function store(
        UploadedFile $file,
        string $directory,
        int $maxWidth,
        int $maxHeight,
        array &$paths,
    ): string {
        $path = $this->images->store($file, $directory, $maxWidth, $maxHeight);
        $paths[] = $path;

        return $path;
    }
}
