<?php

namespace App\Http\Controllers;

use App\Http\Resources\GiftCardResource;
use App\Models\Producto;
use App\Models\VentaProducto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class InformeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('informes', ['productos' => Producto::all(['id', 'nombre'])]);
    }

    public function download(Request $request)
    {
        $pdf = \App::make('dompdf.wrapper');

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
        if ( $request->get('sedes') != '' )
        {
            $sedes = explode(',', $request->get('sedes'));

            if ( count($sedes) > 0 )
            {
                $data->whereIn('sede_id', $sedes);
            }
        }

        // Filtro de productos
        if ( $request->get('productos') != '' )
        {
            $productos = explode(',', $request->get('productos'));

            if ( count($productos) > 0 )
            {
                $data->whereIn('producto_id', $productos);
            }
        }

        // Filtro de fecha de asignación
        if ( $request->get('asig_start') && $request->get('asig_end') )
        {
            $data->whereBetween('fecha_asignacion', [$request->get('asig_start'), $request->get('asig_end')]);
        }

        // Filtro de fecha de vencimiento
        if ( $request->get('venci_start') && $request->get('venci_end') )
        {
            $data->whereBetween('fecha_vencimiento', [$request->get('venci_start'), $request->get('venci_end')]);
        }

        $count = $data->count();
        $data->orderBy($request->sort, $request->direction);
        $results = $data->get();

        $pdf->setPaper('A4', 'landscape');

        $data = $request->all();
        $data['estados_array'] = [1 => 'Válida', 2 => 'Consumida', 3 => 'Asignada', 4 => 'Vencida', 5 => 'Cancelada'];
        $data['results'] = GiftCardResource::collection($results);

        $pdf->loadView('informe_export', $data);
        return $pdf->download();
    }
}
