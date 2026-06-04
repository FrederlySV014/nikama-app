<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DriverRegisterRequest extends FormRequest
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

            // Driver Profile attributes
            'vehicle_type' => ['required', 'string', 'in:bicycle,motorcycle,car'],
            'license_number' => ['required_unless:vehicle_type,bicycle', 'nullable', 'string', 'max:20'],
            'vehicle_brand' => ['nullable', 'string', 'max:100'],
            'vehicle_model' => ['nullable', 'string', 'max:100'],
            'vehicle_color' => ['nullable', 'string', 'max:50'],
            'license_plate' => ['required_unless:vehicle_type,bicycle', 'nullable', 'string', 'max:20', 'unique:driver_profiles,license_plate'],
            'emergency_contact_name' => ['required', 'string', 'max:100'],
            'emergency_contact_phone' => ['required', 'string', 'regex:/^[0-9]{9}$/'],
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
            'phone.required' => 'El número de celular es obligatorio.',
            'phone.regex' => 'El número de celular debe tener exactamente 9 dígitos numéricos.',

            'vehicle_type.required' => 'El tipo de vehículo es obligatorio.',
            'vehicle_type.in' => 'El tipo de vehículo seleccionado no es válido.',
            'license_number.required_unless' => 'La licencia de conducir es obligatoria para este tipo de vehículo.',
            'license_number.max' => 'La licencia de conducir no debe superar los 20 caracteres.',
            'license_plate.required_unless' => 'La placa del vehículo es obligatoria para este tipo de vehículo.',
            'license_plate.max' => 'La placa del vehículo no debe superar los 20 caracteres.',
            'license_plate.unique' => 'Esta placa de vehículo ya está registrada.',
            'emergency_contact_name.required' => 'El nombre de contacto de emergencia es obligatorio.',
            'emergency_contact_name.max' => 'El nombre de contacto de emergencia no debe superar los 100 caracteres.',
            'emergency_contact_phone.required' => 'El celular del contacto de emergencia es obligatorio.',
            'emergency_contact_phone.regex' => 'El celular del contacto de emergencia debe tener exactamente 9 dígitos numéricos.',
        ];
    }
}
