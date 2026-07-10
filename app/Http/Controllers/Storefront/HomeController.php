<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\GoldPriceService;
use App\Services\ProductCatalogService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(ProductCatalogService $catalog, GoldPriceService $goldPrices): View
    {
        $heroProducts = $catalog->query()
            ->inRandomOrder()
            ->limit(5)
            ->get();
        $newProducts = $catalog->query('new')->limit(20)->get();
        $categories = Category::query()
            ->where('is_active', true)
            ->whereHas('products')
            ->orderBy('name')
            ->get();
        $goldRate = $goldPrices->current();
        $goldPrices->applyProductPrices($heroProducts, $goldRate);
        $goldPrices->applyProductPrices($newProducts, $goldRate);

        return view('storefront.home', compact(
            'heroProducts',
            'newProducts',
            'categories',
            'goldRate',
        ));
    }
}
