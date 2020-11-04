<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GiftCardResource;
use App\Models\Venta;
use App\Models\VentaProducto;
use DataTables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;

class GiftCardController extends Controller
{
    public function indexMinoristas(Request $request)
    {
        $data = VentaProducto::giftCards()->pagas()->minoristas()->get();

        return Datatables::of($data)

                ->addColumn('id_tienda_nube', function($row){

                    return $row->venta->external_id;
                })

                ->rawColumns(['id_tienda_nube'])

                ->addColumn('codigo', function($row){

                    return $row->codigo_gift_card;
                })

                ->rawColumns(['codigo'])

                ->addColumn('producto', function($row){

                    return $row->producto->nombre;
                })

                ->rawColumns(['producto'])

                ->addColumn('fecha_venta', function($row){

                    return strtoupper(date('d/m/Y', strtotime($row->venta->created_at)));
                })

                ->rawColumns(['fecha_venta'])

                ->addColumn('concepto', function($row){

                    return $row->venta->source_id == Venta::SOURCE_TIENDA_NUBE ? 'Tienda Nube' :
                        ( $row->venta->source_id == Venta::SOURCE_CANJE ? 'Canje' : (
                            $row->venta->source_id == Venta::SOURCE_INVITACION ? 'Invitación' : 'Venta'
                        ) );
                })

                ->rawColumns(['concepto'])

                ->addColumn('fecha_pago', function($row){

                    return $row->venta->fecha_pago ? strtoupper(date('d/m/Y', strtotime($row->venta->fecha_pago))) : null;
                })

                ->rawColumns(['fecha_pago'])

                ->addColumn('fecha_vencimiento', function($row){

                    return strtoupper(date('d/m/Y', strtotime($row->fecha_vencimiento)));
                })

                ->rawColumns(['fecha_vencimiento'])

                ->addColumn('fecha_asignacion', function($row){

                    return $row->fecha_asignacion ? strtoupper(date('d/m/Y', strtotime($row->fecha_asignacion))) : null;
                })

                ->rawColumns(['fecha_asignacion'])

                ->addColumn('fecha_consumicion', function($row){

                    return $row->fecha_consumicion ? strtoupper(date('d/m/Y', strtotime($row->fecha_consumicion))) : null;
                })

                ->rawColumns(['fecha_consumicion'])

                ->addColumn('usuario_asignacion', function($row){

                    return $row->estado == VentaProducto::ESTADO_ASIGNADA ? $row->asignadaPor->name : null;
                })

                ->rawColumns(['usuario_asignacion'])

                ->addColumn('estado', function($row){

                    return $row->estado == VentaProducto::ESTADO_VALIDA ? 'Valida' : ( $row->estado == VentaProducto::ESTADO_CONSUMIDA ? 'Consumida' : ( $row->estado == VentaProducto::ESTADO_VENCIDA ? 'Vencida' : 'Asignada' ) );
                })

                ->rawColumns(['estado'])

                ->addColumn('sede_mesa', function($row){

                    return $row->estado == VentaProducto::ESTADO_ASIGNADA ? $row->sede->nombre . ' Mesa: ' . $row->nro_mesa : null;
                })

                ->rawColumns(['sede_mesa'])

                ->make(true);
    }

    public function indexMayoristas(Request $request)
    {
        $data = VentaProducto::giftCards()->mayoristas()->get();

        return Datatables::of($data)

                ->addColumn('codigo', function($row){

                    return $row->codigo_gift_card;
                })

                ->rawColumns(['codigo'])

                ->addColumn('producto', function($row){

                    return $row->producto->nombre;
                })

                ->rawColumns(['producto'])

                ->addColumn('fecha_venta', function($row){

                    return strtoupper(date('d/m/Y', strtotime($row->venta->created_at)));
                })

                ->rawColumns(['fecha_venta'])

                ->addColumn('empresa', function($row){

                    return $row->venta->empresa->nombre;
                })

                ->rawColumns(['empresa'])

                ->addColumn('concepto', function($row){

                    return $row->venta->source_id == Venta::SOURCE_TIENDA_NUBE ? 'Tienda Nube' :
                        ( $row->venta->source_id == Venta::SOURCE_CANJE ? 'Canje' : (
                            $row->venta->source_id == Venta::SOURCE_INVITACION ? 'Invitación' : 'Venta'
                        ) );
                })

                ->rawColumns(['concepto'])

                ->addColumn('fecha_pago', function($row){

                    return $row->venta->fecha_pago ? strtoupper(date('d/m/Y', strtotime($row->venta->fecha_pago))) : null;
                })

                ->rawColumns(['fecha_pago'])

                ->addColumn('fecha_vencimiento', function($row){

                    return strtoupper(date('d/m/Y', strtotime($row->fecha_vencimiento)));
                })

                ->rawColumns(['fecha_vencimiento'])

                ->addColumn('fecha_asignacion', function($row){

                    return $row->fecha_asignacion ? strtoupper(date('d/m/Y', strtotime($row->fecha_asignacion))) : null;
                })

                ->rawColumns(['fecha_asignacion'])

                ->addColumn('fecha_consumicion', function($row){

                    return $row->fecha_consumicion ? strtoupper(date('d/m/Y', strtotime($row->fecha_consumicion))) : null;
                })

                ->rawColumns(['fecha_consumicion'])

                ->addColumn('usuario_asignacion', function($row){

                    return $row->estado == VentaProducto::ESTADO_ASIGNADA ? $row->asignadaPor->name : null;
                })

                ->rawColumns(['usuario_asignacion'])

                ->addColumn('estado', function($row){

                    return $row->estado == VentaProducto::ESTADO_CANCELADA ? 'Cancelada' : ( $row->estado == VentaProducto::ESTADO_VALIDA ? 'Valida' : ( $row->estado == VentaProducto::ESTADO_CONSUMIDA ? 'Consumida' : ( $row->estado == VentaProducto::ESTADO_VENCIDA ? 'Vencida' : 'Asignada' ) ) );
                })

                ->rawColumns(['estado'])

                ->addColumn('sede_mesa', function($row){

                    return $row->estado == VentaProducto::ESTADO_ASIGNADA ? $row->sede->nombre . ', Mesa: ' . $row->nro_mesa : null;
                })

                ->rawColumns(['sede_mesa'])

                ->addColumn('nro_factura', function($row){

                    return $row->venta->nro_factura;
                })

                ->rawColumns(['nro_factura'])

                ->addColumn('comentario', function($row){

                    return $row->venta->comentario;
                })

                ->rawColumns(['comentario'])

                ->addColumn('action', function($row){

                    return  $row->estado == VentaProducto::ESTADO_VALIDA ? '<a href="#" class="edit btn btn-danger btn-sm btn_cancel_giftcard" data-url="' . route('api.giftcards.cancel', ['codigo' => $row->codigo_gift_card]) . '" data-toggle="modal" data-target="#cancel_giftcard_modal">Cancelar</a>' : ( $row->estado == VentaProducto::ESTADO_CANCELADA ? $row->motivo_cancelacion . ' ( ' . $row->canceladaPor->name . ', ' . strtoupper(date('d/m/Y', strtotime($row->fecha_cancelacion))) . ' ) ' : null );
                })

                ->rawColumns(['action'])

                ->make(true);
    }

    public function asignar(Request $request, $codigo)
    {
        $gc = VentaProducto::where('codigo_gift_card', $codigo)->firstOrFail();
        $gc->asignar($request->sede, $request->nro_mesa, Auth::id());

        $gc->save();

        return Response::json(new GiftCardResource($gc), 201);
    }

    public function validar($codigo)
    {
        $gc = VentaProducto::where('codigo_gift_card', $codigo)->first();

        return Response::json($gc ? new GiftCardResource($gc) : null, 201);
    }

    public function cancelar(Request $request, $codigo)
    {
        $gc = VentaProducto::where('codigo_gift_card', $codigo)->first();

        if ( $gc->estado != VentaProducto::ESTADO_VALIDA )
        {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "giftcard" => ['Solo puedes cancelar giftcards válidas.'],
            ]);
        }

        $gc->cancelar($request->motivo);
        $gc->usuario_cancelacion = auth()->id();
        $gc->save();

        return Response::json(null, 201);
    }
}
