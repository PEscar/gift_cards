<?php

namespace App\Http\Controllers;

use App\Models\VentaProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GiftCardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ( auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Nivel1') )
        {
            return view('giftcards');
        }

        return redirect()->route('home');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $codigo
     * @return \Illuminate\Http\Response
     */
    public function show($codigo = null)
    {
        $gc = $codigo ? VentaProducto::where('codigo_gift_card', $codigo)->first() : null;

        return view('giftcard', ['gc' => $gc, 'codigo' => $codigo]);
    }

    /**
     * Display qr of random gift card
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_random_qr()
    {
        $gc = VentaProducto::giftCards()->get()->random();

        return view('giftcard_qr', ['gc' => $gc]);
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

    public function consumir($codigo)
    {
        $gc = VentaProducto::where('codigo_gift_card', $codigo)->firstOrFail();
        $gc->fecha_consumicion = \Illuminate\Support\Carbon::now();
        $gc->consumicion_id = Auth::id();
        $gc->save();

        return redirect()->route('giftcards.show', ['codigo' => $gc->codigo_gift_card]);
    }

    public function asignar(Request $request, $codigo)
    {
        $gc = VentaProducto::where('codigo_gift_card', $codigo)->firstOrFail();
        $gc->fecha_asignacion = \Illuminate\Support\Carbon::now();
        $gc->asignacion_id = Auth::id();
        $gc->sede_id = $request->sede;
        $gc->nro_mesa = $request->nro_mesa;
        $gc->save();

        return redirect()->route('giftcards.show', ['codigo' => $gc->codigo_gift_card]);
    }
}
