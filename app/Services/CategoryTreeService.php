<?php

namespace App\Services;

use App\Models\Category;

class CategoryTreeService
{
    public function options(?Category $exclude = null): array
    {
        return Category::query()
            ->when($exclude, fn ($query) => $query->whereKeyNot($exclude->id))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
