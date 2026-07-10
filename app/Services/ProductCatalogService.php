<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProductCatalogService
{
    public function paginate(string $section, ?int $categoryId, int $perPage = 12): LengthAwarePaginator
    {
        return $this->query($section, $categoryId)->paginate($perPage);
    }

    public function query(string $section = 'category', ?int $categoryId = null): Builder
    {
        return Product::query()
            ->with('category')
            ->whereHas('category', fn (Builder $query) => $query->where('is_active', true))
            ->when($section === 'light', fn (Builder $query) => $query
                ->whereNotNull('weight')
                ->where('weight', '<', 3)
                ->orderBy('weight')
                ->latest('id'))
            ->when($section === 'new', fn (Builder $query) => $query->latest())
            ->when($section === 'category', fn (Builder $query) => $query
                ->when($categoryId, fn (Builder $query) => $query->where('category_id', $categoryId))
                ->latest());
    }
}
