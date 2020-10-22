<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductoController extends Controller
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
            return view('productos');
        }

        return redirect()->route('home');
    }
}
