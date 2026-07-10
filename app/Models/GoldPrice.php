<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldPrice extends Model
{
    protected $fillable = ['provider', 'sell_price', 'fetched_at'];

    protected function casts(): array
    {
        return [
            'sell_price' => 'integer',
            'fetched_at' => 'datetime',
        ];
    }
}
