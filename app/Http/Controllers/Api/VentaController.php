<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VentaMayoristaRequest;
use App\Models\Venta;
use App\Models\VentaProducto;
use App\Notifications\GiftCardMailNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Response;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $codigo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function store(VentaMayoristaRequest $request)
    {
        $venta = new Venta;
        $venta->date = Carbon::now();
        $venta->source_id = 1; // Intranet
        $venta->pagada = $request->pagada ? 1 : 0;
        $venta->fecha_pago = $venta->pagada ? Carbon::now() : null;
        $venta->vendedor_id = auth()->id();
        $venta->client_email = $request->client_email;
        $venta->comentario = $request->comentario;

        $venta->save();

        for ($i=1; $i <= $request->cantidad ; $i++) {

            $ventaProducto = factory(VentaProducto::class)->make();
            $ventaProducto->descripcion = null;
            $ventaProducto->sku = $request->sku;
            $ventaProducto->tipo_producto = 1; //gift cards
            $ventaProducto->cantidad = 1;
            $ventaProducto->fecha_vencimiento = \Illuminate\Support\Carbon::now()->addDays(env('VENCIMIENTO_GIFT_CARDS', 30))->toDate();

            $venta->venta_productos()->save($ventaProducto);
        }

        if ( $venta->pagada )
        {
            $venta->pagada = true;

            if ( $venta->tieneGiftcards() )
            {
                $venta->notify(new GiftCardMailNotification);
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
                    $venta->notify(new GiftCardMailNotification);
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
                    $venta->notify(new GiftCardMailNotification);
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
