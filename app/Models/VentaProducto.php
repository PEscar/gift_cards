<?php

namespace App\Models;

use App\User;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class VentaProducto extends Model
{
	protected $table = 'venta_producto';

    const TIPO_GIFTCARD = 1;
    const TIPO_NORMAL = 2;

    const ESTADO_VALIDA = 1;
    const ESTADO_CONSUMIDA = 2;
    const ESTADO_ASIGNADA = 3;
    const ESTADO_VENCIDA = 4;
    const ESTADO_CANCELADA = 5;

    public static function findGiftCardByCodigo($codigo)
    {
    	return self::where('codigo', $codigo)->first();
    }

    // SCOPES

    public function scopeGiftCards($query)
    {
        return $query->whereNotNull('codigo_gift_card');
    }

    public function scopeMinoristas($query)
    {
        return $query->whereHas('venta', function (Builder $query) {
            $query->where('source_id', '=', Venta::SOURCE_TIENDA_NUBE);
        });
    }

    public function scopeMayoristas($query)
    {
        return $query->whereHas('venta', function (Builder $query) {
            $query->where('source_id', '!=', Venta::SOURCE_TIENDA_NUBE);
        });
    }

    public function scopePagas($query)
    {
        return $query->whereHas('venta', function (Builder $query) {
            $query->where('pagada', true);
        });
    }

    // END SCOPES

    // ACCESORS

    public function getEstadoAttribute()
    {
        // Una GC puede tener 3 estados: asignada, vencida o valida.
        $hoy = \Illuminate\Support\Carbon::now()->toDateString();

        if ( !is_null($this->motivo_cancelacion) )
        {
            return self::ESTADO_CANCELADA;
        }
        else if ( !is_null($this->fecha_asignacion) )
        {
            return self::ESTADO_ASIGNADA;
        }
        else if( $this->fecha_vencimiento < $hoy )
        {
            return self::ESTADO_VENCIDA;
        }
        else if( $this->fecha_vencimiento >= $hoy )
        {
            return self::ESTADO_VALIDA;
        }

        return self::ESTADO_CONSUMIDA;
    }

    public function getEstadoLabelAttribute()
    {
        // Una GC puede tener 3 estados: asignada, vencida o valida.
        $hoy = \Illuminate\Support\Carbon::now()->toDateString();

        if ( !is_null($this->motivo_cancelacion) )
        {
            return 'Cancelada';
        }
        else if ( !is_null($this->fecha_asignacion) )
        {
            return 'Asignada';
        }
        else if( $this->fecha_vencimiento < $hoy )
        {
            return 'Vencida';
        }
        else if( $this->fecha_vencimiento >= $hoy )
        {
            return 'Válida';
        }

        return 'Consumida';
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

    public function canceladaPor()
    {
        return $this->belongsTo(User::class, 'usuario_cancelacion', 'id');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    // END RELATIONS

    // FUNCTIONS

    public function generateGiftCardCode()
    {
        do
        {
            $this->codigo_gift_card = \Str::random(8);

            $validator = \Validator::make(['codigo_gift_card' => $this->codigo_gift_card], [
                'codigo_gift_card' => 'unique:venta_producto,codigo_gift_card',
            ]);

        } while ( $validator->fails() );
    }

    public function asignar($sede, $nro_mesa, $user_id)
    {
        $this->fecha_asignacion = \Illuminate\Support\Carbon::now();
        $this->asignacion_id = $user_id;
        $this->sede_id = $sede;
        $this->nro_mesa = $nro_mesa;

        return $this;
    }

    public function desasignar()
    {
        $this->fecha_asignacion = null;
        $this->asignacion_id = null;
        $this->sede_id = null;
        $this->nro_mesa = null;

        return $this;
    }

    public function cancelar($motivo)
    {
        $this->motivo_cancelacion = $motivo;
        $this->fecha_cancelacion = \Illuminate\Support\Carbon::now();

        return $this;
    }

    public function updatePrecioFromTiendaNube()
    {
        $api = new \TiendaNube\API(1222005, env('TIENDA_NUBE_ACCESS_TOKEN', null), 'La Parolaccia (comercial@fscarg.com)');

        echo 'pidiendo datos de orden: ' . $this->venta->external_id . '<br>';

        $order = $api->get("orders/" . $this->venta->external_id);

        // Save products
        foreach ($order->body->products as $key => $orderProduct) {

            if ( $this->producto->sku != $orderProduct->sku )
            {
                echo 'distintos!: ' . $this->producto->sku . ' != ' . $orderProduct->sku . '<br>';
                echo '---------------------------------------<br>';
                continue;
            }

            echo 'id venta producto: ' . $this->id . '<br>';
            echo 'prod: ' . $orderProduct->name . '<br>';
            echo 'mi sku: ' . $this->producto->sku . '<br>';
            echo 'sku: ' . $orderProduct->sku . '<br>';
            echo 'precio: ' . $orderProduct->price . '<br>';

            $this->precio = $orderProduct->price;
            $this->save();
        }
    }

    // END FUNCTIONS
}
