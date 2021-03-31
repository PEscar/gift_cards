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
            'empresa' => 'required',
            'sku' => 'required',
            'concepto' => 'required|lte:3',
            'cantidad' => 'required|gte:1',
            'validez' => 'required|gte:1',
            'pagada' => 'bool',
            'fecha_pago' => 'required_if:pagada,true',
            'comentario' => '',
            'nro_factura' => '',
            'tipo_notificacion' => '',
            'precio' => 'required',
        ];
    }
}
