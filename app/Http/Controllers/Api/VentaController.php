<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateVentaMayoristaRequest;
use App\Http\Requests\VentaMayoristaRequest;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaProducto;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Response;

class VentaController extends Controller
{
    public function store(VentaMayoristaRequest $request)
    {
        if ( ! auth()->user()->hasRole('Admin') )
        {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "user" => ['Tenes que ser admin para poder crear ventas.'],
            ]);
        }

        $producto = Producto::where('sku', $request->sku)->firstOrFail();

        $venta = new Venta;
        $venta->date = Carbon::now();
        $venta->source_id = $request->concepto;
        $venta->pagada = $request->pagada ? 1 : 0;
        $venta->fecha_pago = $venta->pagada ? $request->fecha_pago : null;
        $venta->vendedor_id = auth()->id();
        $venta->empresa_id = $request->empresa;
        $venta->comentario = $request->comentario;
        $venta->nro_factura = $request->nro_factura;
        $venta->tipo_notificacion = $request->tipo_notificacion;

        $ventaProductos = factory(VentaProducto::class, (int) $request->cantidad)->make(['producto_id' => $producto->id, 'cantidad' => 1, 'fecha_vencimiento' => \Illuminate\Support\Carbon::now()->addDays($request->validez)->toDate()]);

        $venta->save();

        foreach ($ventaProductos as $key => $ventaProducto) {

            $ventaProducto->generateGiftCardCode();
            $venta->venta_productos()->save($ventaProducto);
        }

        if ( $venta->tieneGiftcards() )
        {
            $venta->entregarGiftcards();
        }

        return Response::json(null, 201);
    }

    public function importOrderFromTiendaNube(Request $request, $order_id = null)
    {
        \Log::error('llego algo para crear');
        $hmac_header = $request->server('HTTP_X_LINKEDSTORE_HMAC_SHA256');
        $data = file_get_contents('php://input');

        // Validacion temporal
        if ( $hmac_header == hash_hmac('sha256', $data, env('TIENDA_NUBE_CLIENT_SECRET', 'falta')) )
        // if ( $request->server('HTTP_USER_AGENT') == 'LinkedStore Webhook (itmaster@tiendanube.com)' )
        {
            // Obtener id de la venta
            $order_id = json_decode($data, true)['id'];

            \Log::error('create order validado ok ok: ' . $order_id);

            $venta = Venta::where('external_id', $order_id)->first();

            if ( $venta === null )
            {
                \Log::error('venta ya registrada, no hacemos nada: ' . $order_id);
            }
            else
            {
                $venta = Venta::importOrderFromTiendaNubeById($order_id);

                \Log::error('pagada: ' . $venta->pagada);
                \Log::error('tiene gcs' . $venta->tieneGiftcards());

                if ( $venta->pagada )
                {
                    $venta->pagada = true;

                    if ( $venta->tieneGiftcards() )
                    {
                        $venta->entregarGiftcards();
                    }

                    $venta->save();
                }
            }
        }
        else
        {
            \Log::error('Mensaje no validado: ' . $data);
        }
    }

    public function updateOrderFromTiendaNube(Request $request)
    {
        \Log::error('llego algo para update');
        \Log::error('agent ' . $request->server('HTTP_USER_AGENT'));
        $hmac_header = $request->server('HTTP_X_LINKEDSTORE_HMAC_SHA256');

        \Log::error('hmac header: ' . $hmac_header);

        $data = file_get_contents('php://input');

        \Log::info('max header: ' . $hmac_header);

        // Validacion temporal
        if ( $hmac_header == hash_hmac('sha256', $data, env('TIENDA_NUBE_CLIENT_SECRET', 'falta')) )
        // if ( $request->server('HTTP_USER_AGENT') == 'LinkedStore Webhook (itmaster@tiendanube.com)' )
        {
            \Log::error('mensajke de update wvaldiado');
            \Log::error('data: ' . $data);
            $data_decoded = json_decode($data, true);
            \Log::error('data id: ' . $data_decoded['id']);

            $venta = Venta::tiendanube()->where('external_id', $data_decoded['id'])->first();

            if ( $venta === null )
            {
                \Log::error('no hay venta con ref id: ' . $data_decoded['id']);
            }
            else
            {

                \Log::error('venta id: ' . $venta->id);

                // Si la notificacion es de orden pagada
                if ( $data_decoded['event'] == 'order/paid' && !$venta->pagada )
                {
                    $venta->pagada = true;

                    // Si la venta tiene productos que sean gigt cards
                    if ( $venta->tieneGiftcards() )
                    {
                        \Log::info('tiene gift cards !');
                        $venta->entregarGiftcards();
                    }

                    $venta->save();
                }

            }
        }
        else
        {
            \Log::error('Mensaje update no validado: ' . $data);
        }
    }

    public function index(Request $request)
    {
        $data = Venta::mayoristas()->get();

        return Datatables::of($data)

                ->addColumn('id', function($row){

                    return $row->id;
                })

                ->rawColumns(['id'])

                ->addColumn('producto', function($row){

                    return $row->venta_productos->first()->producto->nombre;
                })

                ->rawColumns(['producto'])

                ->addColumn('fecha_venta', function($row){

                    return strtoupper(date('d/m/Y', strtotime($row->created_at)));
                })

                ->rawColumns(['fecha_venta'])

                ->addColumn('empresa', function($row){

                    return $row->empresa->nombre;
                })

                ->rawColumns(['empresa'])

                ->addColumn('concepto', function($row){

                    return $row->source_id == Venta::SOURCE_TIENDA_NUBE ? 'Tienda Nube' :
                        ( $row->source_id == Venta::SOURCE_CANJE ? 'Canje' : (
                            $row->source_id == Venta::SOURCE_INVITACION ? 'Invitación' : 'Venta'
                        ) );
                })

                ->rawColumns(['concepto'])

                ->addColumn('fecha_pago', function($row){

                    return $row->fecha_pago ? strtoupper(date('d/m/Y', strtotime($row->fecha_pago))) : null;
                })

                ->rawColumns(['fecha_pago'])

                ->addColumn('fecha_vencimiento', function($row){

                    return strtoupper(date('d/m/Y', strtotime($row->venta_productos->first()->fecha_vencimiento)));
                })

                ->rawColumns(['fecha_vencimiento'])

                ->addColumn('nro_factura', function($row){

                    return $row->nro_factura;
                })

                ->rawColumns(['nro_factura'])

                ->addColumn('comentario', function($row){

                    return $row->comentario;
                })

                ->rawColumns(['comentario'])

                ->addColumn('codigos', function($row){

                    return  implode(',', $row->giftcards->pluck(['codigo_gift_card'])->toArray());
                })

                ->rawColumns(['codigos'])

                ->addColumn('action', function($row){

                    $user = $row->usuario_edicion_id ? $row->usuario_edicion->name : null;
                    $fecha = $row->fecha_ultima_edicion ? date('d/m/Y', strtotime($row->fecha_ultima_edicion)) : null;

                    return  '<a href="#" data-nro_factura="' . $row->nro_factura . '" class="edit btn btn-warning btn-sm btn_edit_venta" data-url="' . route('api.ventas.update', ['id' => $row->id]) . '" data-pagada="' . $row->pagada . '" data-fecha_pago="' . ($row->fecha_pago ? date('Y-m-d', strtotime($row->fecha_pago)) : '') . '" data-comentario="' . $row->comentario . '" data-toggle="modal" data-target="#update_venta_modal" data-id="' . $row->id . '">Edit</a> ' . $user . ', <b>' . $fecha . '</b>';
                })

                ->rawColumns(['action'])

                ->make(true);
    }

    public function update(UpdateVentaMayoristaRequest $request, $id)
    {
        if ( ! auth()->user()->hasRole('Admin') )
        {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "user" => ['Tenes que ser admin para poder actualizar ventas.'],
            ]);
        }

        $venta = Venta::findOrFail($id);

        $venta->nro_factura = $request->nro_factura;
        $venta->comentario = $request->comentario;
        $venta->usuario_edicion_id = auth()->id();
        $venta->fecha_ultima_edicion = \Illuminate\Support\Carbon::now()->toDate();
        $venta->pagada = $request->pagada != 'false'; //Jquery lo envía como string !!!
        $venta->fecha_pago = $request->fecha_pago;

        $venta->save();

        return Response::json(null, 204);
    }
}
