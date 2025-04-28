<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EstudianteRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nombre' => 'required|string|max:100|regex:/^[^0-9]*$/',
            'edad' => 'required|integer|min:1|max:120',
        ];
    }

    /**
     * Obtiene los mensajes personalizados para los errores del validador.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.regex' => 'El nombre no debe contener números',
            'edad.required' => 'La edad es obligatoria',
            'edad.integer' => 'La edad debe ser un número entero',
            'edad.min' => 'La edad debe ser mínimo de 1 año',
            'edad.max' => 'La edad no debe ser menor a 120 años',
        ];
    }
}