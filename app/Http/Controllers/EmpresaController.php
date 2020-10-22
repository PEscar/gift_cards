<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmpresaController extends Controller
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
            return view('empresas');
        }

        return redirect()->route('home');
    }
}
