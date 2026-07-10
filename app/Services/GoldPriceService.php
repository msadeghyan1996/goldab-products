<?php

namespace App\Services;

use App\Models\GoldPrice;

class GoldPriceService
{
    public function current(): ?array
    {
        $rate = GoldPrice::query()->where('provider', 'irangold')->first();

        $price = $rate ? intdiv($rate->sell_price, 10) : null;

        return $rate ? [
            'price' => $price,
            'gram_price' => $this->roundUpToThousand($price / 4.3318),
            'updated_at' => $rate->fetched_at->format('H:i'),
            'is_live' => $rate->fetched_at->greaterThanOrEqualTo(now()->subMinutes(2)),
        ] : null;
    }

    public function productPrice(float $weight, float $wagePercentage, int $goldRate): int
    {
        $pricePerGram = $goldRate / 4.3318;
        $wageMultiplier = 1 + ($wagePercentage / 100);

        return (int) round($pricePerGram * $wageMultiplier * $weight);
    }

    public function applyProductPrices(iterable $products, ?array $goldRate = null): void
    {
        $goldRate ??= $this->current();

        foreach ($products as $product) {
            $product->setAttribute(
                'calculated_price',
                $goldRate && $product->weight !== null
                    ? $this->productPrice(
                        (float) $product->weight,
                        (float) ($product->wage_percentage ?? 0),
                        $goldRate['price'],
                    )
                    : null,
            );
        }
    }

    private function roundUpToThousand(float $price): int
    {
        return (int) (ceil($price / 1000) * 1000);
    }
}
