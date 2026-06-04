<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RejectRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rejected_reason' => ['required', 'string', 'min:5', 'max:1000'],
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
            'rejected_reason.required' => 'El motivo de rechazo es obligatorio.',
            'rejected_reason.string' => 'El motivo de rechazo debe ser una cadena de texto.',
            'rejected_reason.min' => 'El motivo de rechazo debe tener al menos 5 caracteres.',
            'rejected_reason.max' => 'El motivo de rechazo no debe superar los 1000 caracteres.',
        ];
    }
}
