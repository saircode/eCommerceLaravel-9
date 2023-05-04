<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class userController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $AllUsers = User::latest()->paginate(25);

        return Inertia::render('Users', [
            'Users'=> $AllUsers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = self::validateData($request->all());
        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $user = new User($request->all());
        $user -> save();
        
        return response (true , 200);
    }

    /**
     * Valida campos obligatorios y sus tipos
     */
    static public function validateData($data) {
        
        $fields = Validator::make($data, [
            "name" => "required|string|max:100",
            "email" => "required|email|unique:users",
            "password" => "required|string|min:8",
           
        ], [
            'name.required' => 'Ingresa el nombre del usuario',
            'name.string' => 'El nombre del usuario contiene caracteres no validos.',
            'email.required' => 'Ingresa el correo del usuario',
            'email.email' => 'El valor ingresado no es un correo valido.',
            'email.unique' => 'El correo ya se encuntra en uso.',
            'password.required'=>'Ingresa la contraseña del usuario',
            'password.string'=>'La contraseña del usuario contiene caracteres no validos.',
            'password.min'=>'Minimo 8 caracteres',
        ]);

        return $fields;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
