<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        $routeCategory = $this->route('category');
        $categoryId = is_object($routeCategory) ? $routeCategory->id : $routeCategory;

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => [
                'required',
                'string',
                'max:120',
                'unique:categories,slug,'.($categoryId ?: 'NULL').',id',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],
            'parent_id' => [
                'nullable',
                'uuid',
                'exists:categories,id',
                // Validación para evitar que una categoría sea su propio padre
                function ($attribute, $value, $fail) use ($categoryId) {
                    if ($categoryId && $value === $categoryId) {
                        $fail('Una categoría no puede ser su propio padre.');
                    }
                },
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'icon' => ['nullable', 'string', 'max:100'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get the custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no debe superar los 100 caracteres.',
            'slug.required' => 'El slug es obligatorio.',
            'slug.string' => 'El slug debe ser una cadena de texto.',
            'slug.max' => 'El slug no debe superar los 120 caracteres.',
            'slug.unique' => 'Este slug ya está en uso por otra categoría.',
            'slug.regex' => 'El formato del slug no es válido. Debe contener solo letras minúsculas, números y guiones (ej. mi-categoria).',
            'parent_id.uuid' => 'La categoría padre seleccionada no es válida.',
            'parent_id.exists' => 'La categoría padre seleccionada no existe en el sistema.',
            'description.string' => 'La descripción debe ser una cadena de texto.',
            'description.max' => 'La descripción no debe superar los 1000 caracteres.',
            'icon.string' => 'El icono debe ser una cadena de texto.',
            'icon.max' => 'El identificador del icono no debe superar los 100 caracteres.',
            'image_url.url' => 'La URL de la imagen no tiene un formato válido.',
            'image_url.max' => 'La URL de la imagen no debe superar los 2048 caracteres.',
            'sort_order.integer' => 'El orden de clasificación debe ser un número entero.',
            'sort_order.min' => 'El orden de clasificación no puede ser negativo.',
        ];
    }
}
