<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;

class ErrorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ( auth()->user()->hasRole('Admin') )
        {
            return view('errores')->with('message', 'The success message!');
        }

        return redirect()->route('home');
    }

    public function retry($id)
    {
        if ( auth()->user()->hasRole('Admin') )
        {
            $venta = Venta::findOrFail($id);
            $venta->fecha_error = null;
            $venta->error_envio = null;
            $venta->save();

            $venta->entregarGiftcards();

            return redirect()->route('errores.index')->with('success', 'Reintentando envío... verifica en unos minutos');
        }

        return redirect()->route('home');
    }
}
