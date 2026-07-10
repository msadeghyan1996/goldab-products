<?php

namespace App\Http\Requests\Product;

use App\Models\Product;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateProductRequest extends StoreProductRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('product')) ?? false;
    }

    public function rules(): array
    {
        /** @var Product $product */
        $product = $this->route('product');

        return $this->commonRules() + [
            'main_image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_main_image' => ['nullable', 'boolean'],
            'gallery' => ['nullable', 'array', 'max:3'],
            'gallery.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_gallery' => ['nullable', 'array'],
            'remove_gallery.*' => ['integer', Rule::exists('product_images', 'id')->where('product_id', $product->id)],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Product $product */
            $product = $this->route('product');
            $remaining = $product->images()->whereNotIn('id', $this->input('remove_gallery', []))->count();
            $incoming = count($this->file('gallery', []));

            if ($remaining + $incoming > 3) {
                $validator->errors()->add('gallery', 'حداکثر سه تصویر گالری مجاز است.');
            }
        });
    }
}
