<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Notifications\GiftCardMailNotification;
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

    public function store(Request $request)
    {
        //
    }

    public function importOrderFromTiendaNube(Request $request)
    {
        // echo 'llego algo para crear';
        \Log::error('llego algo para crear');
        // $hmac_header = $request->header('HTTP_X_LINKEDSTORE_HMAC_SHA256');;
        // $data = file_get_contents('php://input');
        // // // dd($data);
        // if ( $hmac_header == hash_hmac('sha256', $data, env('yllh6Sr1u0TSzYOQ6zyr1bPS2hQ42nmSPsOfomL2BPDdNy4x', 'falta')) )
        // {
        //     \Log::error('create order validado ok ok');
        //     // Obtener id de la venta
        //     $order_id = 279936732;
        //     $venta = Venta::importOrderFromTiendaNubeById($order_id);

        //     echo $venta->pagada.'|';
        //     echo $venta->tieneGiftcards();

        //     if ( $venta->pagada && $venta->tieneGiftcards() )
        //     {
        //         echo 'si';
        //         $venta->notify(new GiftCardMailNotification);
        //     }
        // }
        // else
        // {
        //     echo 'mensjage no validado';
        //     \Log::error('Mensaje no validado: ' . json_encode($data));
        // }
    }

    public function updateOrderFromTiendaNube(Request $request)
    {
        \Log::error('llego algo para update');
        // $hmac_header = $request->header('HTTP_X_LINKEDSTORE_HMAC_SHA256');;
        // $data = file_get_contents('php://input');

        // if ( $hmac_header == hash_hmac('sha256', $data, env('yllh6Sr1u0TSzYOQ6zyr1bPS2hQ42nmSPsOfomL2BPDdNy4x', 'falta')) )
        // {
        //     \Log::error('mensajke de update wvaldiado');
        //     \Log::error('data: ' . $data);
        //     \Log::error('data: ' . json_decode($data, true)['id']);
        //     // Obtener id de la venta
        //     // $order_id = 279936732;
        //     // $venta = Venta::importOrderFromTiendaNubeById($order_id);

        //     // echo $venta->pagada.'|';
        //     // echo $venta->tieneGiftcards();

        //     // if ( $venta->pagada && $venta->tieneGiftcards() )
        //     // {
        //     //     echo 'si';
        //     //     $venta->notify(new GiftCardMailNotification);
        //     // }
        // }
        // else
        // {
        //     \Log::error('Mensaje update no validado');
        // }
    }
}
