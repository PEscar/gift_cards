<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\VentaProducto;
use DataTables;
use Illuminate\Http\Request;
use Response;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Producto::all();

        return Datatables::of($data)

                ->addColumn('id', function($row){

                    return $row->id;
                })

                ->rawColumns(['id'])

                ->addColumn('sku', function($row){

                    return $row->sku;
                })

                ->rawColumns(['sku'])

                ->addColumn('nombre', function($row){

                    return $row->nombre;
                })

                ->rawColumns(['nombre'])

                ->addColumn('descripcion', function($row){

                    return $row->descripcion;
                })

                ->rawColumns(['descripcion'])

                ->addColumn('action', function($row){

                    $btn = '<a href="#" class="edit btn btn-primary btn-sm btn_view_producto" data-toggle="modal" data-target="#view_producto_modal" data-sku="' . $row->sku . '" data-id="' . $row->id . '" data-nombre="' . $row->nombre . '" data-descripcion="' . $row->descripcion . '">View</a> <a href="#" data-sku="' . $row->sku . '" class="edit btn btn-warning btn-sm btn_edit_producto" data-url="' . route('api.productos.update', ['id' => $row->id]) . '" data-nombre="' . $row->nombre . '" data-descripcion="' . $row->descripcion . '" data-toggle="modal" data-target="#update_producto_modal" data-id="' . $row->id . '">Edit</a> <a href="#" class="edit btn btn-danger btn-sm btn_del_user" data-url="' . route('api.productos.destroy', ['id' => $row->id]) . '" data-id="' . $row->id . '" data-toggle="modal" data-target="#delete_producto_modal">Delete</a>' ;

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
                "user" => ['Tenes que ser admin para poder actualizar Productos.'],
            ]);
        }

        $producto = Producto::where('sku', $request->sku)->where('id', '!=', $id)->first();

        if ( $producto )
        {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "sku" => ['Código en uso.'],
            ]);
        }

        $producto = Producto::findOrFail($id);

        $producto->update(['nombre' => $request->nombre, 'descripcion' => $request->descripcion, 'sku' => $request->sku]);

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
                "user" => ['Tenes que ser admin para poder borrar productos.'],
            ]);
        }

        $producto = Producto::findOrFail($id);
        $ventaProductos_cant = VentaProducto::where('producto_id', $producto->id)->count();

        if ( $ventaProductos_cant > 0 )
        {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "producto" => ['No se puede eliminar un producto con ventas registradas.'],
            ]);
        }

        $producto->delete();

        return Response::json(null, 204);
    }

    public function store(Request $request)
    {
        $producto = Producto::where('sku', $request->sku)->first();

        if ( $producto )
        {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "sku" => ['Código en uso.'],
            ]);
        }

        $producto = new Producto;
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->sku = $request->sku;
        $producto->save();

        return Response::json(null, 201);
    }
}
