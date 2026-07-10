<?php

namespace App\Http\Requests\Product;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Product::class) ?? false;
    }

    public function rules(): array
    {
        return $this->commonRules() + [
            'main_image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gallery' => ['nullable', 'array', 'max:3'],
            'gallery.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    protected function commonRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:999999999.999'],
            'wage_percentage' => ['nullable', 'numeric', 'between:0,100'],
            'availability' => ['required', 'in:'.Product::AVAILABLE.','.Product::UNAVAILABLE],
        ];
    }
}
