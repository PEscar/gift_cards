<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Venta extends Model
{
	use Notifiable;

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        return $this->client_email;
    }

	// RELATIONS

	public function venta_productos()
	{
		return $this->hasMany(VentaProducto::class);
	}

	// END RELATIONS

    // SCOPES

    /**
     * Ventas from Tienda Nube
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTiendaNube($query)
    {
        return $query->where('source_id', 0);
    }

    /**
     * Ventas pagadas 
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePagadas($query)
    {
        return $query->where('pagada', 1);
    }

    // END SCOPES

    public function tieneGiftcards()
    {
        $tiene = false;

        foreach ($this->venta_productos as $key => $ventaProducto) {
            
            if ( $ventaProducto->tipo_producto == 1 )
            {
                $tiene = true;
            }
        }

        return $tiene;
    }

    // Método que importa una orden desde tienda nube, a partir de su ID.
    // Si la venta contiene giftcards, les genera un codigo y fecha de vencimient oa cada una. el tiemp ode validez es configurable
    // desde .env
    public static function importOrderFromTiendaNubeById($order_id)
    {
        $skus_gift_cards = ['11251', '11247', '11248', '11249', '11250'];

        $api = new \TiendaNube\API(1222005, env('TIENDA_NUBE_ACCESS_TOKEN', null), 'La Parolaccia (comercial@fscarg.com)');
        $order = $api->get("orders/" . $order_id);

        $venta = new Venta;
        $venta->external_id = $order->body->id;
        $venta->date = date('Y-m-d H:i:s', strtotime($order->body->created_at));
        $venta->source_id = 0; // Tienda Nube
        $venta->pagada = $order->body->payment_status == 'paid' ? true : false;
        $venta->client_email = $order->body->customer->email;
        $venta->save();

        // Save products
        foreach ($order->body->products as $key => $orderProduct) {
            
            // Para generar el codigo formato isbn10 unico con faker
            $ventaProducto = factory(VentaProducto::class)->make();
            $ventaProducto->sku = $orderProduct->sku;
            $ventaProducto->descripcion = $orderProduct->name;
            $ventaProducto->cantidad = $orderProduct->quantity;

            // Si es git card
            if ( in_array($ventaProducto->sku, $skus_gift_cards) )
            {
                $ventaProducto->tipo_producto = 1; // Gift Card
                $ventaProducto->fecha_vencimiento = \Illuminate\Support\Carbon::now()->addDays(env('VENCIMIENTO_GIFT_CARDS', 30))->toDate();
            }
            else {

                $ventaProducto->tipo_producto = 0; //Producto común
                $ventaProducto->fecha_vencimiento = null;
                $ventaProducto->codigo_gift_card = null;
            }

            $venta->venta_productos()->save($ventaProducto);
        }

        return $venta;
    }
}
