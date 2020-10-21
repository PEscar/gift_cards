<?php

namespace App\Models;

use App\Notifications\GiftCardMailNotification;
use App\Notifications\GiftCardZipMailNotification;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Venta extends Model
{
	use Notifiable;

    const SOURCE_TIENDA_NUBE = 0;
    const SOURCE_CANJE = 1;
    const SOURCE_INVITACION = 2;
    const SOURCE_MAYORISTA = 3;

    const PAGADA_NO = 0;
    const PAGADA_SI = 1;

    const TIPO_NOTIFICACION_PDF_ATTACH = 1;
    const TIPO_NOTIFICACION_ZIP_LINK = 2;

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

    // METHODS

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

    public function entregarGiftcards()
    {
        if ( $this->tipo_notificacion == self::TIPO_NOTIFICACION_PDF_ATTACH )
        {
            $this->notify(new GiftCardMailNotification);
        } else
        {
            $this->notify(new GiftCardZipMailNotification);
        }
    }

    public function generatePdfs()
    {
        $renderer = new ImageRenderer(
            new RendererStyle(140, 0, null),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        $pdfs = [];

        foreach ($this->venta_productos as $key => $ventaProduct) {

            // Si es gift card
            if ( $ventaProduct->tipo_producto == 1 )
            {
                $qr_code = new \Illuminate\Support\HtmlString($writer->writeString(route('giftcards.show', ['codigo' => $ventaProduct->codigo_gift_card])));

                $pdf = PDF::loadView('emails.giftcard_pdf', ['qr_code' => $qr_code, 'notifiable' => $this, 'item' => $ventaProduct]);
                $pdfs[] = ['pdf' => $pdf, 'pdf_filename' => $ventaProduct->codigo_gift_card . '.pdf'];
            }
        }

        return $pdfs;
    }

    // Método que importa una orden desde tienda nube, a partir de su ID.
    // Si la venta contiene giftcards, les genera un codigo y fecha de vencimient oa cada una. el tiemp ode validez es configurable
    // desde .env
    public static function importOrderFromTiendaNubeById($order_id)
    {
        $skus_gift_cards = ['11247', '11255', '11256', '11257', '11251', '11252', '11253', '11254'];

        $api = new \TiendaNube\API(1222005, env('TIENDA_NUBE_ACCESS_TOKEN', null), 'La Parolaccia (comercial@fscarg.com)');
        $order = $api->get("orders/" . $order_id);

        $venta = new Venta;
        $venta->external_id = $order->body->id;
        $venta->date = date('Y-m-d H:i:s', strtotime($order->body->created_at));
        $venta->source_id = 0; // Tienda Nube
        $venta->pagada = $order->body->payment_status == 'paid' ? true : false;
        $venta->client_email = $order->body->customer->email;
        $venta->comentario = $order->body->note;
        $venta->fecha_pago = $venta->pagada ? date('Y-m-d H:i:s', strtotime($order->body->paid_at)) : null;
        $venta->save();

        // Save products
        foreach ($order->body->products as $key => $orderProduct) {

            // Si el producto es tipo giftcard, creo un ventaProduct por cada unidad de este producto, para
            // generarle a cada gift card su código
            if ( in_array($orderProduct->sku, $skus_gift_cards) )
            {
                for ($i=1; $i <= $orderProduct->quantity ; $i++)
                {
                    // Para generar el codigo formato isbn10 unico con faker
                    $ventaProduct = new VentaProducto;
                    $ventaProduct->sku = $orderProduct->sku;
                    $ventaProduct->descripcion = $orderProduct->name;
                    $ventaProduct->cantidad = 1;
                    $ventaProduct->tipo_producto = 1; // Gift Card
                    $ventaProduct->fecha_vencimiento = \Illuminate\Support\Carbon::now()->addDays(env('VENCIMIENTO_GIFT_CARDS', 30))->toDate();
                    $ventaProduct->generateGiftCardCode();
                    $venta->venta_productos()->save($ventaProduct);
                }
            }
            else
            {
                // Si es producto normal, guardamos sku nombre y cantidad.
                $ventaProduct = new VentaProducto;
                $ventaProduct->sku = $orderProduct->sku;
                $ventaProduct->descripcion = $orderProduct->name;
                $ventaProduct->cantidad = $orderProduct->quantity;
                $ventaProduct->tipo_producto = 2; //Producto común
                $venta->venta_productos()->save($ventaProduct);
            }
        }

        return $venta;
    }
}
