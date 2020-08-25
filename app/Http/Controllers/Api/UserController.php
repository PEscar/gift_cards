<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePassowrdRequest;
use App\Http\Requests\UserRequest;
use App\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Response;
use Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = User::all();

        return Datatables::of($data)

                ->addColumn('id', function($row){

                    return $row->id;
                })

                ->rawColumns(['id'])

                ->addColumn('admin', function($row){

                    return $row->hasRole('Admin') ? 'Si' : 'No';
                })

                ->rawColumns(['admin'])

                ->addColumn('sedes', function($row){

                    return $row->sedes()->count();
                })

                ->rawColumns(['sedes'])

                ->addColumn('action', function($row){

                       $btn = '<a href="#" class="edit btn btn-primary btn-sm btn_view_user" data-toggle="modal" data-target="#view_user_modal" data-id="' . $row->id . '" data-name="' . $row->name . '" data-email="' . $row->email . '" data-admin="' . $row->hasRole('Admin') . '" data-sedes="' . implode(',', $row->sedes->pluck('nombre')->toArray()) . '">View</a> <a href="#" class="edit btn btn-warning btn-sm btn_edit_user" data-url="' . route('api.users.update', ['id' => $row->id]) . '" data-name="' . $row->name . '" data-email="' . $row->email . '" data-admin="' . $row->hasRole('Admin') . '" data-sedes="' . implode(',', $row->sedes->pluck('id')->toArray()) . '" data-toggle="modal" data-target="#update_user_modal" data-id="' . $row->id . '">Edit</a> <a href="#" class="edit btn btn-danger btn-sm btn_del_user" data-url="' . route('api.users.destroy', ['id' => $row->id]) . '" data-id="' . $row->id . '" data-toggle="modal" data-target="#delete_user_modal">Delete</a>'; 

                        return $btn;
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
        $user = User::where('email', $request->email)->where('id', '!=', $id)->first();

        if ( $user )
        {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "email" => ['Email en uso.'],
            ]);
        }

        $user = User::findOrFail($id);

        $user->name = $request->name;
        $user->email = $request->email;

        $user->save();

        if ( $request->admin )
        {
            $user->assignRole('Admin');
        }
        else 
        {
            $user->removeRole('Admin');
        }

        $user->sedes()->sync($request->sedes);

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
                "user" => ['Tenes que ser admin para poder borra usuarios.'],
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

        $user = new User;
        $user->email = $request->email;
        $user->name = $request->name;
        $user->email_verified_at = date('Y-m-d H:i:s');
        $user->password = Hash::make('123123');
        $user->api_token = Str::random(60);
        $user->save();

        if ( $request->admin )
        {
            $user->assignRole('Admin');
        }

        $user->sedes()->sync($request->sedes);

        return Response::json(null, 201);
    }

    // Process update password form submit
    public function updatePassword(UpdatePassowrdRequest $request)
    {
        $user = auth()->user();

        if ( !Hash::check($request->password_actual, $user->password) )
        {
            throw \Illuminate\Validation\ValidationException::withMessages([
                "contraseña_actual" => ['Su contraseña actual es incorrecta.'],
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return Response::json(null, 200);
    }
}
