<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateUserInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'userName' => 'required|string|max:255',
            'identification' => 'required|integer|',
            'age' => 'required|numeric|between:1,99',
            'sex' => 'required|string|in:Hombre,Mujer',
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return[
            'identification.integer' => 'Escribe tu número de identificación sin puntos, comas, espacios o letras',
            'age.between' => 'La edad debe ser un número entre 1 y 99',
        ];
    }

}
