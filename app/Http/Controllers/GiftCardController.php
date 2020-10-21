<?php

namespace App\Http\Controllers;

use App\Models\VentaProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GiftCardController extends Controller
{
    public function indexMinoristas()
    {
        if ( auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Nivel1') )
        {
            return view('giftcards_minoristas');
        }

        return redirect()->route('home');
    }

    public function indexMayoristas()
    {
        if ( auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Nivel1') )
        {
            return view('giftcards_mayoristas');
        }

        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $codigo
     * @return \Illuminate\Http\Response
     */
    public function show($codigo = null)
    {
        $estados = [
            'valida' => VentaProducto::ESTADO_VALIDA,
            'consumida' => VentaProducto::ESTADO_CONSUMIDA,
            'asignada' => VentaProducto::ESTADO_ASIGNADA,
            'vencida' => VentaProducto::ESTADO_VENCIDA
        ];

        return view('giftcard', ['codigo' => $codigo, 'estados' => $estados, 'sedes' => auth()->user()->sedes]);
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
}
