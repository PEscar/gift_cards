<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use DataTables;
use Illuminate\Http\Request;
use Response;

class ErrorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Venta::envioFallido()->get();

        return Datatables::of($data)

                ->addColumn('id', function($row){

                    return $row->id;
                })

                ->rawColumns(['id'])

                ->addColumn('error', function($row){

                    return $row->error_envio;
                })

                ->rawColumns(['error'])

                ->addColumn('fecha', function($row){

                    return strtoupper(date('d/M/Y H:i:s', strtotime($row->fecha_error)));
                })

                ->rawColumns(['fecha'])

                ->addColumn('action', function($row){

                    $btn = '<a href="' . route('errores.retry', ['id' => $row->id]) . '" class="btn btn-danger">Reintentar</a>' ;

                    return $btn;
                })

                ->rawColumns(['action'])

                ->make(true);
    }
}
