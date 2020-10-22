<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VentaMayoristaRequest;
use App\Jobs\SendGiftCardMailNotification;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaProducto;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Response;

class VentaController extends Controller
{
    public function store(VentaMayoristaRequest $request)
    {
        $producto = Producto::where('sku', $request->sku)->firstOrFail();

        $venta = new Venta;
        $venta->date = Carbon::now();
        $venta->source_id = $request->concepto;
        $venta->pagada = $request->pagada ? 1 : 0;
        $venta->fecha_pago = $venta->pagada ? $request->fecha_pago : null;
        $venta->vendedor_id = auth()->id();
        $venta->empresa_id = $request->empresa;
        $venta->comentario = $request->comentario;
        $venta->nro_factura = $request->nro_factura;
        $venta->tipo_notificacion = $request->tipo_notificacion;

        $venta->save();

        for ($i=1; $i <= $request->cantidad ; $i++) {

            $ventaProducto = factory(VentaProducto::class)->make();
            $ventaProducto->producto_id = $producto->id;
            $ventaProducto->cantidad = 1;
            $ventaProducto->fecha_vencimiento = \Illuminate\Support\Carbon::now()->addDays($request->validez)->toDate();
            $ventaProducto->generateGiftCardCode();

            $venta->venta_productos()->save($ventaProducto);
        }

        if ( $venta->pagada )
        {
            $venta->pagada = true;

            if ( $venta->tieneGiftcards() )
            {
                SendGiftCardMailNotification::dispatch($venta);
            }

            $venta->save();
        }

        return Response::json(null, 201);
    }

    public function importOrderFromTiendaNube(Request $request, $order_id = null)
    {
        \Log::error('llego algo para crear');
        \Log::error('agent ' . $request->server('HTTP_USER_AGENT'));
        $hmac_header = $request->server('HTTP_X_LINKEDSTORE_HMAC_SHA256');

        \Log::error('hmac header: ' . $hmac_header);

        $data = file_get_contents('php://input');

        // Validacion temporal
        if ( $hmac_header == hash_hmac('sha256', $data, env('TIENDA_NUBE_CLIENT_SECRET', 'falta')) )
        // if ( $request->server('HTTP_USER_AGENT') == 'LinkedStore Webhook (itmaster@tiendanube.com)' )
        {
            \Log::error('create order validado ok ok');
            // Obtener id de la venta
            $order_id = json_decode($data, true)['id'];
            $venta = Venta::importOrderFromTiendaNubeById($order_id);

            \Log::error('pagada: ' . $venta->pagada);
            \Log::error('tiene gcs' . $venta->tieneGiftcards());

            if ( $venta->pagada )
            {
                $venta->pagada = true;

                if ( $venta->tieneGiftcards() )
                {
                    SendGiftCardMailNotification::dispatch($venta);
                }

                $venta->save();
            }
        }
        else
        {
            \Log::error('Mensaje no validado: ' . $data);
        }
    }

    public function updateOrderFromTiendaNube(Request $request)
    {
        \Log::error('llego algo para update');
        \Log::error('agent ' . $request->server('HTTP_USER_AGENT'));
        $hmac_header = $request->server('HTTP_X_LINKEDSTORE_HMAC_SHA256');

        \Log::error('hmac header: ' . $hmac_header);

        $data = file_get_contents('php://input');

        \Log::info('max header: ' . $hmac_header);

        // Validacion temporal
        if ( $hmac_header == hash_hmac('sha256', $data, env('TIENDA_NUBE_CLIENT_SECRET', 'falta')) )
        // if ( $request->server('HTTP_USER_AGENT') == 'LinkedStore Webhook (itmaster@tiendanube.com)' )
        {
            \Log::error('mensajke de update wvaldiado');
            \Log::error('data: ' . $data);
            $data_decoded = json_decode($data, true);
            \Log::error('data id: ' . $data_decoded['id']);

            $venta = Venta::tiendanube()->where('external_id', $data_decoded['id'])->firstOrFail();

            \Log::error('venta id: ' . $venta->id);

            // Si la notificacion es de orden pagada
            if ( $data_decoded['event'] == 'order/paid' )
            {
                $venta->pagada = true;

                // Si la venta tiene productos que sean gigt cards
                if ( $venta->tieneGiftcards() )
                {
                    \Log::info('tiene gift cards !');
                    SendGiftCardMailNotification::dispatch($venta);
                }

                $venta->save();
            }
        }
        else
        {
            \Log::error('Mensaje update no validado: ' . $data);
        }
    }
}
