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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ( ! auth()->user()->hasRole('Admin') )
        {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "user" => ['Tenes que ser admin para poder actualizar empresas.'],
            ]);
        }

        $empresa = Empresa::where('email', $request->email)->where('id', '!=', $id)->first();

        if ( $empresa )
        {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "email" => ['Email en uso.'],
            ]);
        }

        $empresa = Empresa::findOrFail($id);

        $empresa->update(['nombre' => $request->nombre, 'email' => $request->email]);

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
        if ( ! auth()->user()->hasRole('Admin') )
        {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "user" => ['Tenes que ser admin para poder borrar empresas.'],
            ]);
        }

        $empresa = Empresa::findOrFail($id);
        $ventas_cant = Venta::where('empresa_id', $empresa->id)->count();

        if ( $ventas_cant > 0 )
        {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "empresa" => ['No se puede eliminar una empresa con ventas registradas.'],
            ]);
        }

        $empresa->delete();

        return Response::json(null, 204);
    }

    public function store(Request $request)
    {
        $empresa = Empresa::where('email', $request->email)->first();

        if ( $empresa )
        {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "email" => ['Email en uso.'],
            ]);
        }

        $producto = new Empresa;
        $producto->nombre = $request->nombre;
        $producto->email = $request->email;
        $producto->save();

        return Response::json(null, 201);
    }
}
