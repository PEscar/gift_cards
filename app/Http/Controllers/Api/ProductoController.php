<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
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
        $data = User::all();

        return Producto::of($data)

                ->addColumn('id', function($row){

                    return $row->id;
                })

                ->rawColumns(['id'])

                ->addColumn('nivel', function($row){

                    return $row->hasRole('Admin') ? 'Admin' : ( $row->hasRole('Nivel1') ? 'Nivel 1' : 'Nivel 2' );
                })

                ->rawColumns(['nivel'])

                ->addColumn('sedes', function($row){

                    return $row->sedes()->count();
                })

                ->rawColumns(['sedes'])

                ->addColumn('action', function($row){

                    $nivel = $row->hasRole('Admin') ? 'Admin' : ( $row->hasRole('Nivel1') ? 'Nivel 1' : 'Nivel 2' );
                    $nivel2 = $row->hasRole('Admin') ? 'Admin' : ( $row->hasRole('Nivel1') ? 'Nivel1' : 'Nivel2' );

                       $btn = '<a href="#" class="edit btn btn-primary btn-sm btn_view_user" data-toggle="modal" data-target="#view_user_modal" data-id="' . $row->id . '" data-name="' . $row->name . '" data-email="' . $row->email . '" data-nivel="' . $nivel . '" data-sedes="' . implode(',', $row->sedes->pluck('nombre')->toArray()) . '">View</a> <a href="#" class="edit btn btn-warning btn-sm btn_edit_user" data-url="' . route('api.users.update', ['id' => $row->id]) . '" data-name="' . $row->name . '" data-email="' . $row->email . '" data-nivel="' . $nivel2 . '" data-sedes="' . implode(',', $row->sedes->pluck('id')->toArray()) . '" data-toggle="modal" data-target="#update_user_modal" data-id="' . $row->id . '">Edit</a> <a href="#" class="edit btn btn-danger btn-sm btn_del_user" data-url="' . route('api.users.destroy', ['id' => $row->id]) . '" data-id="' . $row->id . '" data-toggle="modal" data-target="#delete_user_modal">Delete</a>'; 

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
    public function update(UserRequest $request, $id)
    {
        if ( ! auth()->user()->hasRole('Admin') )
        {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "user" => ['Tenes que ser admin para poder actualizar usuarios.'],
            ]);
        }

        $user = User::where('email', $request->email)->where('id', '!=', $id)->first();

        if ( $user )
        {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "email" => ['Email en uso.'],
            ]);
        }

        $user = User::findOrFail($id);

        $user->update(['name' => $request->name, 'email' => $request->email]);

        $user->setNivel($request->nivel);
        $user->setSedes($request->sedes);

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
                "user" => ['Tenes que ser admin para poder borrar usuarios.'],
            ]);
        }

        $user = User::findOrFail($id);
        
        if ( auth()->user()->hasRole('Admin') && $user->id == auth()->user()->id )
        {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "user" => ['No podés borrarte a vos mismo.'],
            ]);
        }

        $user->delete();

        return Response::json(null, 204);
    }

    public function store(UserRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if ( $user )
        {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "email" => ['Email en uso.'],
            ]);
        }

        if ( ! auth()->user()->hasRole('Admin') )
        {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "user" => ['Tenes que ser admin para poder crear usuarios.'],
            ]);
        }

        $user = new User;
        $user->email = $request->email;
        $user->name = $request->name;
        $user->email_verified_at = date('Y-m-d H:i:s');
        $user->password = Hash::make('123123');
        $user->api_token = Str::random(60);
        $user->save();

        $user->setNivel($request->nivel);
        $user->setSedes($request->sedes);

        return Response::json(null, 201);
    }
}
