<?php

namespace App\Http\Resources;

use App\Models\Venta;
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
            'estado_label' => $this->estado_label,
            'consumio' => $this->consumidaPor ? $this->consumidaPor->name : null,
            'asigno' => $this->asignadaPor ? $this->asignadaPor->name : null,
            'fecha_consumicion' => $this->consumidaPor ? strtoupper(date('d/m/Y', strtotime($this->fecha_consumicion))) : null,
            'fecha_vencimiento' => strtoupper(date('d/m/Y', strtotime($this->fecha_vencimiento))),
            'fecha_asignacion' => $this->asignadaPor ? strtoupper(date('d/m/Y', strtotime($this->fecha_asignacion))) : null,
            'fecha_cancelacion' => $this->canceladaPor ? strtoupper(date('d/m/Y', strtotime($this->fecha_cancelacion))) : null,
            'fecha_venta' => strtoupper(date('d/m/Y', strtotime($this->venta->date))),
            'fecha_venta_full' => $this->venta->date,
            'cantidad' => $this->cantidad,
            'descripcion' => $this->producto->nombre,
            'nro_mesa' => $this->nro_mesa,
            'sede' => $this->sede ? $this->sede->nombre : null,
            'cancelo' => $this->canceladaPor ? $this->canceladaPor->name : null,
            'motivo_cancelacion' => $this->motivo_cancelacion,
            'concepto' => $this->venta->source_id == Venta::SOURCE_TIENDA_NUBE ? 'Tienda Nube' : ( $this->venta->source_id == Venta::SOURCE_CANJE ? 'Canje' : ( $this->venta->source_id == Venta::SOURCE_INVITACION ? 'Invitación' : 'Venta' )),
            'precio' => $this->precio, // ? number_format($this->precio, 2, ',', '.') : '-'
        ];
    }
}
