<?php

namespace App\Http\Controllers;

use App\Http\Resources\GiftCardResource;
use App\Models\Producto;
use App\Models\Sede;
use App\Models\VentaProducto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function getDataToExportFromRequest(Request $request)
    {
        $data = VentaProducto::whereNotNull('codigo_gift_card');

        // Filtro de estados
        if ( $request->get('estados') )
        {
            $estados = explode(',', $request->get('estados'));

            foreach ($estados as $key => $estado) {

                switch ($estado) {

                    // Cancelada
                    case VentaProducto::ESTADO_CANCELADA:

                        $data->whereNotNull('motivo_cancelacion');
                        break;

                    // Asignada
                    case VentaProducto::ESTADO_ASIGNADA:
                        $data->whereNotNull('fecha_asignacion');
                        break;

                    // Vencida
                    case VentaProducto::ESTADO_VENCIDA:
                        $hoy = \Illuminate\Support\Carbon::now()->toDateString();
                        $data->where('fecha_vencimiento', '<', $hoy)
                            ->whereNull('fecha_asignacion')
                            ->whereNull('motivo_cancelacion');
                        break;

                    // Valida
                    case VentaProducto::ESTADO_VALIDA:
                        $hoy = \Illuminate\Support\Carbon::now()->toDateString();
                        $data->where('fecha_vencimiento', '>=', $hoy)
                            ->whereNull('fecha_asignacion')
                            ->whereNull('motivo_cancelacion');
                        break;

                    // Consumida
                    case VentaProducto::ESTADO_CONSUMIDA:
                        $data->whereNotNull('fecha_asignacion')
                            ->whereNull('motivo_cancelacion');
                        break;

                    default:
                        # code...
                        break;
                }
            }
        }

        // Filtro de conceptos
        if ( $request->get('conceptos') != '' )
        {
            $conceptos = explode(',', $request->get('conceptos'));

            if ( count($conceptos) > 0 )
            {
                $data->whereHas('venta', function (Builder $query) use ($conceptos) {
                    $query->whereIn('source_id', $conceptos);
                });
            }
        }

        // Filtro de sedes
        $sedes = explode(',', $request->get('sedes'));
        if ( count($sedes) > 0 )
        {
            // La opción con valor 0, es "Sin Sede"
            if ( in_array('0', $sedes) )
            {
                $data->where(function ($query) use ($sedes) {
                    $query->whereIn('sede_id', $sedes)
                        ->orWhereNull('sede_id');
               });
            }
            else
            {
                $data->whereIn('sede_id', $sedes);
            }
        }

        // Filtro de productos
        $productos = explode(',', $request->get('productos'));
        if ( count($productos) > 0 )
        {
            $data->whereIn('producto_id', $productos);
        }

        // Filtro de fecha de asignación
        if ( $request->get('asig_start') && $request->get('asig_end') )
        {
            $data->whereBetween('fecha_asignacion', [$request->get('asig_start') . ' 00:00:00', $request->get('asig_end') . ' 23:59:59']);
        }

        // Filtro de fecha de vencimiento
        if ( $request->get('venci_start') && $request->get('venci_end') )
        {
            $data->whereBetween('fecha_vencimiento', [$request->get('venci_start'), $request->get('venci_end')]);
        }

        // Filtro de fecha de cancelación
        if ( $request->get('cance_start') && $request->get('cance_end') )
        {
            $data->whereBetween('fecha_cancelacion', [$request->get('cance_start'), $request->get('cance_end')]);
        }

        $data->join('ventas', 'ventas.id', '=', 'venta_producto.venta_id');

        // Filtro de fecha de venta
        if ( $request->get('venta_start') && $request->get('venta_end') )
        {
            $venta_start = $request->get('venta_start');
            $venta_end = $request->get('venta_end');

            $data->whereBetween('ventas.date', [$venta_start . ' 00:00:00', $venta_end . ' 23:59:59']);
        }

        $count = $data->count();
        $data->orderBy('ventas.date', $request->get('direction'));
        $results = $data->get();

        $data = $request->all();
        $data['estados_array'] = [1 => 'V&aacute;lida', 2 => 'Consumida', 3 => 'Asignada', 4 => 'Vencida', 5 => 'Cancelada'];
        $data['results'] = GiftCardResource::collection($results);

        $sedes_labels = Sede::whereIn('id', $sedes)->get()->pluck('nombre')->all();

        if ( in_array(0, $sedes) )
        {
            $sedes_labels[] = 'Sin Sede';
        }

        $data['sedes'] = implode(', ', $sedes_labels);
        $data['productos'] = implode(', ', Producto::whereIn('id', $productos)->get()->pluck('nombre')->all());

        return $data;
    }
}
