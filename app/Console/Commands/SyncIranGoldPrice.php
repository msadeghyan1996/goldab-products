<?php

namespace App\Console\Commands;

use App\Models\GoldPrice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Throwable;

class SyncIranGoldPrice extends Command
{
    protected $signature = 'gold:sync-price';

    protected $description = 'Fetch and store the first molten sell price from IranGold API';

    public function handle(): int
    {
        $token = config('gold.token');
        if (! $token) {
            $this->error('IRANGOLD_API_TOKEN is not configured.');

            return self::FAILURE;
        }

        try {
            $response = Http::acceptJson()
                ->withHeaders(['token' => $token])
                ->withOptions(['verify' => (bool) config('gold.verify_ssl')])
                ->connectTimeout(3)
                ->timeout(10)
                ->get(config('gold.api_url'))
                ->throw();

            $rawPrice = $response->json('data.moltens.0.sell_price');
            $sellPrice = (int) preg_replace('/[^0-9]/', '', (string) $rawPrice);
            if ($sellPrice <= 0) {
                throw new \RuntimeException('data.moltens.0.sell_price is missing or invalid.');
            }

            GoldPrice::updateOrCreate(
                ['provider' => 'irangold'],
                ['sell_price' => $sellPrice, 'fetched_at' => now()],
            );

            $this->info('IranGold sell price updated successfully.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            $this->error('IranGold price update failed: '.$exception->getMessage());

            return self::FAILURE;
        }
    }
}
