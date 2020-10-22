<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Venta;
use DataTables;
use Illuminate\Http\Request;
use Response;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Empresa::all();

        return Datatables::of($data)

                ->addColumn('id', function($row){

                    return $row->id;
                })

                ->rawColumns(['id'])

                ->addColumn('nombre', function($row){

                    return $row->nombre;
                })

                ->rawColumns(['nombre'])

                ->addColumn('email', function($row){

                    return $row->email;
                })

                ->rawColumns(['email'])

                ->addColumn('action', function($row){

                    $btn = '<a href="#" class="edit btn btn-primary btn-sm btn_view_producto" data-toggle="modal" data-target="#view_empresa_modal" data-nombre="' . $row->nombre . '" data-id="' . $row->id . '" data-email="' . $row->email . '">View</a> <a href="#" data-nombre="' . $row->nombre . '" class="edit btn btn-warning btn-sm btn_edit_empresa" data-url="' . route('api.empresas.update', ['id' => $row->id]) . '" data-email="' . $row->email . '" data-toggle="modal" data-target="#update_empresa_modal" data-id="' . $row->id . '">Edit</a> <a href="#" class="edit btn btn-danger btn-sm btn_del_user" data-url="' . route('api.empresas.destroy', ['id' => $row->id]) . '" data-id="' . $row->id . '" data-toggle="modal" data-target="#delete_empresa_modal">Delete</a>' ;

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
