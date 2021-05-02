<?php

namespace App\Http\Controllers;

use App\Http\Resources\GiftCardResource;
use App\Models\Producto;
use App\Models\Sede;
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
        if ( auth()->user()->hasRole('Admin') )
        {
            return view('informes', ['productos' => Producto::all(['id', 'nombre'])]);
        }
        else
        {
            return redirect()->route('home');
        }
    }

    public function downloadPdf(Request $request)
    {
        \Log::channel('informes')->info('informe pdf generado! ' . json_encode($request->all()));

        $data = $this->getDataToExportFromRequest($request);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->setPaper('A4', 'landscape');
        $pdf->loadView('informe_export', $data);
        return $pdf->download();
    }

    public function downloadExcel(Request $request)
    {
        \Log::channel('informes')->info('informe excel generado! ' . json_encode($request->all()));

        $data = $this->getDataToExportFromRequest($request);

        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=informe.xlsx");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);

        echo view('informe_export', $data);
    }
}
