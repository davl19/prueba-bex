<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;


class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define las reglas de validación para los campos de entrada del formulario de inicio de sesión.
     *
     * @return array Un arreglo con las reglas de validación para los campos 'username' y 'password'.
     */
    public function rules(): array
    {
        return [
            'username' => 'required',
            'password' => 'required',
        ];
    }

    /**
     * Define los mensajes personalizados de validación para los campos de entrada del formulario de inicio de sesión.
     *
     * @return array Un arreglo con los mensajes personalizados de validación para los campos 'username' y 'password'.
     */
    public function messages(): array
    {
        return [
            'username.required' => __('username.required'),
            'password.required' => __('password.required'),
        ];
    }
}
