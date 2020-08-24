<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VentaProducto;
use DataTables;
use Illuminate\Http\Request;
use Response;

class GiftCardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = VentaProducto::giftCards()->get();

        return Datatables::of($data)

                ->addColumn('id', function($row){

                    return $row->id;
                })

                ->rawColumns(['id'])

                ->addColumn('codigo', function($row){

                    return $row->codigo_gift_card;
                })

                ->rawColumns(['codigo'])

                ->addColumn('fecha_venta', function($row){

                    return strtoupper(date('d/M/Y H:i', strtotime($row->venta->created_at)));
                })

                ->rawColumns(['fecha_venta'])

                ->addColumn('fecha_pago', function($row){

                    return $row->venta->fecha_pago ? strtoupper(date('d/M/Y H:i', strtotime($row->venta->fecha_pago))) : null;
                })

                ->rawColumns(['fecha_pago'])

                ->addColumn('fecha_vencimiento', function($row){

                    return strtoupper(date('d/M/Y', strtotime($row->venta->fecha_vencimiento)));
                })

                ->rawColumns(['fecha_vencimiento'])

                ->addColumn('fecha_canje', function($row){

                    return $row->fecha_canje ? strtoupper(date('d/M/Y', strtotime($row->fecha_canje))) : null;
                })

                ->rawColumns(['fecha_canje'])

                ->addColumn('usuario_entrega', function($row){

                    return $row->entregadoPor ? $row->entregadoPor->name : null;
                })

                ->rawColumns(['usuario_entrega'])

                ->addColumn('estado', function($row){

                    return $row->valida ? 'Valida' : ( $row->consumida ? 'Consumida' : ( $row->vencida ? 'Vencida' : 'No se' ) );
                })

                ->rawColumns(['estado'])

                ->addColumn('action', function($row){

                       return null;
                })

                ->rawColumns(['action'])

                ->make(true);
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
    public function update(UserRequest $request, $id)
    {
        //

        return Response::json(null, 204);
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

        return Response::json(null, 204);
    }

    public function store(UserRequest $request)
    {
        //

        return Response::json(null, 201);
    }
}
