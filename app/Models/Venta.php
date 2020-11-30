<?php

namespace App\Models;

use App\Jobs\GenerateVoucher;
use App\Jobs\SendGiftCardZipMailNotification;
use App\Notifications\GiftCardMailNotification;
use App\User;
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

    protected $fillable = ['update'];

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        return $this->source_id == self::SOURCE_TIENDA_NUBE ? $this->client_email : $this->empresa->email;
    }

	// RELATIONS

	public function venta_productos()
	{
		return $this->hasMany(VentaProducto::class);
	}

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function usuario_edicion()
    {
        return $this->belongsTo(User::class, 'usuario_edicion_id', 'id');
    }

    public function giftcards()
    {
        return $this->hasMany(VentaProducto::class)->whereNotNull('codigo_gift_card');
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
     * Ventas Mayoristas
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMayoristas($query)
    {
        return $query->where('source_id','!=', 0);
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
            
            if ( $ventaProducto->producto->tipo_producto == Producto::TIPO_GIFTCARD )
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
            $this->notify(new GiftCardMailNotification($this));
        } else
        {
            GenerateVoucher::withChain([
                new SendGiftCardZipMailNotification($this)
            ])->dispatch($this);
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
            if ( $ventaProduct->producto->tipo_producto == Producto::TIPO_GIFTCARD )
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
        $venta->refresh();

        // Save products
        foreach ($order->body->products as $key => $orderProduct) {

            // Si el producto es tipo giftcard, creo un ventaProduct por cada unidad de este producto, para
            // generarle a cada gift card su código

            $producto = Producto::where('sku', $orderProduct->sku)->first();

            if ( $producto )
            {
                if ( $producto->tipo_producto == Producto::TIPO_GIFTCARD )
                {
                    for ($i=1; $i <= $orderProduct->quantity ; $i++)
                    {
                        $ventaProduct = new VentaProducto;
                        $ventaProduct->producto_id = $producto->id;
                        $ventaProduct->cantidad = 1;
                        $ventaProduct->fecha_vencimiento = \Illuminate\Support\Carbon::now()->addDays(env('VENCIMIENTO_GIFT_CARDS', 30))->toDate();
                        $ventaProduct->generateGiftCardCode();
                        $venta->venta_productos()->save($ventaProduct);
                    }
                }
                else
                {
                    $ventaProduct->cantidad = $orderProduct->quantity;
                    $venta->venta_productos()->save($ventaProduct);
                }
            }
            else
            {
                \Log::info('no se encontro producto con sku: ' . $orderProduct->sku);
            }
        }

        return $venta;
    }
}
