<?php

namespace App\Http\Requests\Seller;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductComboRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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

        $businessId = $this->input('business_id');

        return [
            'name' => ['required', 'string', 'max:150'],
            'business_id' => [
                'required',
                'uuid',
                Rule::in($businessIds),
            ],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'is_active' => ['boolean'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => [
                'required',
                'uuid',
                Rule::exists('products', 'id')->where(function ($query) use ($businessId) {
                    if ($businessId) {
                        return $query->where('business_id', $businessId);
                    }

                    return $query;
                }),
            ],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
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
            'price' => 'precio del combo',
            'is_active' => 'activo',
            'products' => 'productos',
            'products.*.product_id' => 'producto',
            'products.*.quantity' => 'cantidad',
        ];
    }
}
