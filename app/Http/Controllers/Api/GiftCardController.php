<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
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

                // ->addColumn('id', function($row){

                //     return $row->venta->id;
                // })

                // ->rawColumns(['id'])

                ->addColumn('codigo', function($row){

                    return $row->codigo_gift_card;
                })

                ->rawColumns(['codigo'])

                ->addColumn('producto', function($row){

                    return $row->descripcion;
                })

                ->rawColumns(['producto'])

                ->addColumn('fecha_venta', function($row){

                    return strtoupper(date('d/M/Y H:i', strtotime($row->venta->created_at)));
                })

                ->rawColumns(['fecha_venta'])

                ->addColumn('concepto', function($row){

                    return $row->venta->source_id == Venta::SOURCE_TIENDA_NUBE ? 'Tienda Nube' :
                        ( $row->venta->source_id == Venta::SOURCE_CANJE ? 'Canje' : (
                            $row->venta->source_id == Venta::SOURCE_INVITACION ? 'Invitación' : 'Mayorista'
                        ) );
                })

                ->rawColumns(['concepto'])

                ->addColumn('fecha_pago', function($row){

                    return $row->venta->fecha_pago ? strtoupper(date('d/M/Y H:i', strtotime($row->venta->fecha_pago))) : null;
                })

                ->rawColumns(['fecha_pago'])

                ->addColumn('fecha_vencimiento', function($row){

                    return strtoupper(date('d/M/Y', strtotime($row->fecha_vencimiento)));
                })

                ->rawColumns(['fecha_vencimiento'])

                ->addColumn('fecha_asignacion', function($row){

                    return $row->fecha_asignacion ? strtoupper(date('d/M/Y H:i', strtotime($row->fecha_asignacion))) : null;
                })

                ->rawColumns(['fecha_asignacion'])

                ->addColumn('fecha_consumicion', function($row){

                    return $row->fecha_consumicion ? strtoupper(date('d/M/Y H:i', strtotime($row->fecha_consumicion))) : null;
                })

                ->rawColumns(['fecha_consumicion'])

                ->addColumn('usuario_asignacion', function($row){

                    return $row->asignadaPor ? $row->asignadaPor->name : null;
                })

                ->rawColumns(['usuario_asignacion'])

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
