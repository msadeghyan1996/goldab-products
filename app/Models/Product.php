<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    public const AVAILABLE = 'available';

    public const UNAVAILABLE = 'unavailable';

    protected $fillable = [
        'category_id', 'title', 'short_description', 'main_image',
        'weight', 'weight_unit', 'wage_percentage', 'availability',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:3',
            'wage_percentage' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }
}
