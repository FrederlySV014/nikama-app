<?php

namespace App\Http\Requests\Seller;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Security is handled by policies/gate middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $businessIds = $user->businesses()
            ->where('businesses.is_active', true)
            ->where('business_users.is_active', true)
            ->pluck('businesses.id')
            ->toArray();
        $product = $this->route('product');
        $productId = $product instanceof Product ? $product->id : $product;

        return [
            'name' => ['required', 'string', 'max:150'],
            'business_id' => [
                'required',
                'uuid',
                Rule::in($businessIds),
            ],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'compare_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products')->where(function ($query) {
                    return $query->where('business_id', $this->input('business_id'));
                })->ignore($productId),
            ],
            'track_stock' => ['boolean'],
            'stock_quantity' => ['required_if:track_stock,1,true', 'integer', 'min:0'],
            'allow_backorder' => ['boolean'],
            'status' => ['required', 'string', Rule::in(Product::statuses())],
            'is_featured' => ['boolean'],
            'requires_preparation' => ['boolean'],
            'preparation_time_minutes' => ['nullable', 'integer', 'min:0'],
            'weight_grams' => ['nullable', 'numeric', 'min:0'],
            'category_id' => [
                'required',
                'uuid',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    $hasChildren = Category::where('parent_id', $value)->exists();
                    if ($hasChildren) {
                        $fail('La categoría seleccionada debe ser de último nivel (no puede tener subcategorías).');
                    }
                },
            ],
            'main_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'additional_images' => ['nullable', 'array'],
            'additional_images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'business_id' => 'negocio',
            'description' => 'descripción',
            'price' => 'precio',
            'compare_price' => 'precio comparativo',
            'sku' => 'SKU',
            'stock_quantity' => 'cantidad en stock',
            'status' => 'estado',
            'category_id' => 'categoría',
            'main_image' => 'imagen principal',
            'additional_images' => 'imágenes adicionales',
        ];
    }
}
