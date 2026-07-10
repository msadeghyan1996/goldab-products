<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\GoldPriceService;
use App\Services\ProductCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request, ProductCatalogService $catalog, GoldPriceService $goldPrices): View
    {
        if (! $request->boolean('all')) {
            return app(HomeController::class)($catalog, $goldPrices);
        }

        [$section, $categoryId] = $this->filters($request);
        $products = $catalog->paginate($section, $categoryId);
        $goldPrices->applyProductPrices($products);
        $categories = Category::query()->where('is_active', true)->orderBy('name')->get();
        $title = match ($section) {
            'light' => 'محصولات کم‌وزن زیر ۳ گرم',
            'new' => 'جدیدترین محصولات',
            default => $categoryId
                ? 'محصولات '.Category::whereKey($categoryId)->value('name')
                : 'همه محصولات',
        };
        $nextPageUrl = $this->nextPageUrl($request, $products->hasMorePages());

        return view('storefront.catalog', compact(
            'products', 'categories', 'section', 'categoryId', 'title', 'nextPageUrl',
        ));
    }

    public function load(Request $request, ProductCatalogService $catalog, GoldPriceService $goldPrices): JsonResponse
    {
        [$section, $categoryId] = $this->filters($request);
        $perPage = min(max($request->integer('per_page', 12), 1), 24);
        $products = $catalog->paginate($section, $categoryId, $perPage);
        $goldPrices->applyProductPrices($products);

        return response()->json([
            'html' => view('storefront.partials.product-cards', compact('products'))->render(),
            'next_page_url' => $this->nextPageUrl($request, $products->hasMorePages()),
        ]);
    }

    private function filters(Request $request): array
    {
        $section = in_array($request->query('section'), ['light', 'new', 'category'], true)
            ? $request->query('section')
            : 'category';
        $categoryId = $request->filled('category_id')
            ? Category::query()->where('is_active', true)->whereKey($request->integer('category_id'))->value('id')
            : null;

        return [$section, $categoryId];
    }

    private function nextPageUrl(Request $request, bool $hasMorePages): ?string
    {
        if (! $hasMorePages) {
            return null;
        }

        return route('storefront.catalog.load', [
            ...$request->except('page'),
            'page' => $request->integer('page', 1) + 1,
        ]);
    }
}
