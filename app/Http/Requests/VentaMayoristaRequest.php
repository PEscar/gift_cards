<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VentaMayoristaRequest extends FormRequest
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
            'cantidad' => 'required|gte:1',
            'sku' => 'required',
            'client_email' => 'required|email',
            'comentario' => '',
        ];
    }
}
