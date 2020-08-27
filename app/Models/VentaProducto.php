<?php

namespace App\Models;

use App\Models\Sede;
use App\User;
use Illuminate\Database\Eloquent\Model;

class VentaProducto extends Model
{
	protected $table = 'venta_producto';

    public static function findGiftCardByCodigo($codigo)
    {
    	return self::where('codigo', $codigo)->first();
    }

    // SCOPES

    public function scopeGiftCards($query)
    {
    	return $query->whereNotNull('codigo_gift_card');
    }

    // END SCOPES

    // ACCESORS

    public function getValidaAttribute()
    {
        return $this->tipo_producto == 1 && $this->fecha_vencimiento >= date('Y-m-d') && $this->fecha_asignacion == null && $this->fecha_consumicion == null;
    }

    public function getConsumidaAttribute()
    {
        return $this->fecha_consumicion != null;
    }

    public function getAsignadaAttribute()
    {
        return $this->fecha_asignacion != null;
    }

    public function getVencidaAttribute()
    {
        return $this->fecha_vencimiento < date('Y-m-d');
    }

    // END ACCESORS

    // RELATIONS

    public function asignadaPor()
    {
        return $this->belongsTo(User::class, 'asignacion_id', 'id');
    }

    public function consumidaPor()
    {
        return $this->belongsTo(User::class, 'consumicion_id', 'id');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    // END RELATIONS

    public static function generateGiftCardCode()
    {
        do
        {
            $codigo_gift_card = \Str::random(8);

            $validator = \Validator::make(['codigo_gift_card' => $codigo_gift_card], [
                'codigo_gift_card' => 'unique:venta_producto,codigo_gift_card',
            ]);

        } while ( $validator->fails() );

        return $codigo_gift_card;
    }
}
