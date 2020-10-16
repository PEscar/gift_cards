<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GiftCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'codigo' => $this->codigo_gift_card,
            'estado' => $this->estado,
            'consumio' => $this->consumidaPor ? $this->consumidaPor->name : null,
            'asigno' => $this->asignadaPor ? $this->asignadaPor->name : null,
            'fecha_consumicion' => $this->consumidaPor ? strtoupper(date('d/M/Y H:i', strtotime($this->fecha_consumicion))) : null,
            'fecha_vencimiento' => strtoupper(date('d/M/Y', strtotime($this->fecha_vencimiento))),
            'fecha_asignacion' => $this->asignadaPor ? strtoupper(date('d/M/Y H:i', strtotime($this->fecha_asignacion))) : null,
            'cantidad' => $this->cantidad,
            'descripcion' => $this->descripcion,
            'nro_mesa' => $this->nro_mesa,
            'sede' => $this->sede ? $this->sede->nombre : null,
        ];
    }
}
