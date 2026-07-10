<?php

use Illuminate\Support\Facades\Http;

test('command stores first molten sell price from IranGold API', function () {
    config()->set('gold.token', 'test-token');
    Http::fake([
        config('gold.api_url') => Http::response([
            'success' => true,
            'data' => [
                'moltens' => [
                    ['sell_price' => 754_950_000],
                    ['sell_price' => 123],
                ],
            ],
        ]),
    ]);

    $this->artisan('gold:sync-price')->assertSuccessful();

    $this->assertDatabaseHas('gold_prices', [
        'provider' => 'irangold',
        'sell_price' => 754_950_000,
    ]);

    Http::assertSent(fn ($request) => $request->hasHeader('token', 'test-token'));
});
