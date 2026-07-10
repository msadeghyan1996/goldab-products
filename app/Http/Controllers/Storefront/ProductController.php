<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\GoldPriceService;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product, GoldPriceService $goldPrices): View
    {
        abort_unless($product->category()->where('is_active', true)->exists(), 404);
        $product->load(['category', 'images']);
        $goldRate = $goldPrices->current();
        $productPrice = $goldRate && $product->weight !== null
            ? $goldPrices->productPrice(
                (float) $product->weight,
                (float) ($product->wage_percentage ?? 0),
                $goldRate['price'],
            )
            : null;
        $relatedProducts = Product::query()
            ->with('category')
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->id)
            ->latest()
            ->limit(4)
            ->get();
        $goldPrices->applyProductPrices($relatedProducts, $goldRate);

        return view('storefront.product', compact('product', 'goldRate', 'productPrice', 'relatedProducts'));
    }
}
