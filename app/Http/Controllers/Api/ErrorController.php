<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\VentaProducto;
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
        $data = Venta::reenvio()->get();

        return Datatables::of($data)

                ->addColumn('id', function($row){

                    return $row->external_id;
                })

                ->rawColumns(['id'])

                ->addColumn('error', function($row){

                    return $row->error_envio;
                })

                ->rawColumns(['error'])

                ->addColumn('fecha', function($row){

                    return strtoupper(date('d/M/Y H:i:s', strtotime($row->date)));
                })

                ->rawColumns(['fecha'])

                ->addColumn('fecha_envio', function($row){

                    return $row->fecha_envio ? strtoupper(date('d/M/Y H:i:s', strtotime($row->fecha_envio))) : '';
                })

                ->rawColumns(['fecha_envio'])

                ->addColumn('fecha_resync', function($row){

                    return $row->resync ? strtoupper(date('d/M/Y', strtotime($row->created_at))) : '';
                })

                ->rawColumns(['fecha_resync'])

                ->make(true);
    }
}
