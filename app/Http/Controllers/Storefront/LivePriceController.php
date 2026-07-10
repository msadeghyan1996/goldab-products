<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\GoldPriceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LivePriceController extends Controller
{
    public function __invoke(Request $request, GoldPriceService $goldPrices): JsonResponse
    {
        $validated = $request->validate([
            'product_ids' => ['nullable', 'array', 'max:50'],
            'product_ids.*' => ['integer', 'distinct'],
        ]);
        $goldRate = $goldPrices->current();
        $products = Product::query()
            ->whereIn('id', $validated['product_ids'] ?? [])
            ->get(['id', 'weight', 'wage_percentage']);

        return response()->json([
            'rate' => $goldRate ? [
                'gram_price' => $goldRate['gram_price'],
                'updated_at' => $goldRate['updated_at'],
                'is_live' => $goldRate['is_live'],
            ] : null,
            'products' => $products->mapWithKeys(fn (Product $product) => [
                $product->id => $goldRate && $product->weight !== null
                    ? $goldPrices->productPrice(
                        (float) $product->weight,
                        (float) ($product->wage_percentage ?? 0),
                        $goldRate['price'],
                    )
                    : null,
            ]),
        ]);
    }
}
