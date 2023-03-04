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

    const FIRST_RESYNC = '2021-11-03 00:00:00';

    protected $fillable = ['update', 'fecha_envio'];

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

    /**
     * Ventas que ha fallado el envio
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnvioFallido($query)
    {
        return $query->whereNotNull('error_envio')
            ->whereNotNull('fecha_error');
    }

    /**
     * Ventas pagas que no se han enviado
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnvioPendiente($query)
    {
        return $query->whereRaw('( ( source_id = 0 AND pagada = 1 ) OR ( source_id in (1, 2, 3) ) )')
            ->whereNull('fecha_envio')
            ->where('date', '>=', '2023-03-01 00:00:00')
            ->has('giftcards');
    }

    /**
     * Ventas pagas que son reenvio. Puede que se hayan enviado o no (envio fallado)
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReenvio($query)
    {
        return $query->where('reenvio', 1);
    }

    /**
     * Ventas enviadas
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnviadas($query)
    {
        return $query->whereNotNull('fecha_envio');
    }

    /**
     * Ventas enviadas
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotTryingSend($query)
    {
        return $query->where('trying_send', 0);
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

    public function entregarGiftcards($reenvio = false)
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

        if ($reenvio)
            $this->reenvio = true;
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
    public static function importOrderFromTiendaNubeById($order_id, $resync = false)
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
        $venta->resync = $resync;
        $venta->reenvio = $resync;
        $venta->error_envio = 'Pedido Resincronizado';
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
                        $ventaProduct->precio = $orderProduct->price;
                        $venta->venta_productos()->save($ventaProduct);
                    }
                }
                else
                {
                    $ventaProduct = new VentaProducto;
                    $ventaProduct->producto_id = $producto->id;
                    $ventaProduct->cantidad = $orderProduct->quantity;
                    $ventaProduct->precio = $orderProduct->price;
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

    // Método que imrpota ordenes de tiendanube que por algún motivo no llegaron las notificaciones
    // Consulta pedidos pagos a partir de una determinada fecha. Luego se fija que no estén ya registrados por external_id, y si no lo están, los guarda y luego serán enviadas sus correspondientes giftcads
    public static function importOrderFromTiendaNubeByDate()
    {
        $api = new \TiendaNube\API(1222005, env('TIENDA_NUBE_ACCESS_TOKEN', null), 'La Parolaccia (comercial@fscarg.com)');

        // Obtenemos la última orden resincronizada, que es hasta donde estamos seguros que no nos falta ninguna
        $date_from = self::where('resync', 1)->max('date') ?: self::FIRST_RESYNC;
        $page = 1;

        do
        {
            $params = ['created_at_min' => $date_from, 'fields' => 'id,created_at', 'payment_status' => 'paid', 'page' => $page];

            $orders = $api->get('orders', $params);

            \Log::info('Resincronizando pedidos desde: ' . $date_from);

            foreach ($orders->body as $key => $venta) {

                \Log::info('Venta: ' . $venta->id);

                if ( !Venta::where('external_id', '=', $venta->id)->exists() )
                {
                    \Log::info('Venta con ID TN: ' . $venta->id . ' resincronizada');
                    Venta::importOrderFromTiendaNubeById($venta->id, true);
                }
            }

            $page++;

        } while (true);

        return true;
    }
}
