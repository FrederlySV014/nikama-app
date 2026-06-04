<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SellerRegisterRequest extends FormRequest
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
        return [
            // User attributes
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{9}$/'],

            // Business attributes
            'business_name' => ['required', 'string', 'max:150'],
            'legal_name' => ['required', 'string', 'max:150'],
            'ruc' => ['required', 'string', 'regex:/^[0-9]{11}$/', 'unique:businesses,ruc'],
            'description' => ['nullable', 'string'],
            'contact_email' => ['required', 'string', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'regex:/^[0-9]{9}$/'],
            'whatsapp_number' => ['nullable', 'string', 'regex:/^[0-9]{9}$/'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'El nombre es obligatorio.',
            'first_name.max' => 'El nombre no debe superar los 100 caracteres.',
            'last_name.required' => 'El apellido es obligatorio.',
            'last_name.max' => 'El apellido no debe superar los 100 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico es inválido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'phone.required' => 'El celular personal es obligatorio.',
            'phone.regex' => 'El celular personal debe tener exactamente 9 dígitos numéricos.',

            'business_name.required' => 'El nombre comercial del negocio es obligatorio.',
            'business_name.max' => 'El nombre comercial no debe superar los 150 caracteres.',
            'legal_name.required' => 'La razón social del negocio es obligatoria.',
            'legal_name.max' => 'La razón social no debe superar los 150 caracteres.',
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.regex' => 'El RUC debe tener exactamente 11 dígitos numéricos.',
            'ruc.unique' => 'Este RUC ya está registrado.',
            'contact_email.required' => 'El correo de contacto del negocio es obligatorio.',
            'contact_email.email' => 'El formato del correo de contacto es inválido.',
            'contact_phone.required' => 'El teléfono de contacto del negocio es obligatorio.',
            'contact_phone.regex' => 'El teléfono de contacto del negocio debe tener exactamente 9 dígitos numéricos.',
            'whatsapp_number.regex' => 'El número de WhatsApp debe tener exactamente 9 dígitos numéricos.',
        ];
    }
}
