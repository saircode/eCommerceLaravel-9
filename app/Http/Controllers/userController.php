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
    static public function validateData($data, $returnOnlyMessage = false) {
        $messages = [
            'name.required' => 'Ingresa el nombre del usuario',
            'name.string' => 'El nombre del usuario contiene caracteres no validos.',
            'email.required' => 'Ingresa el correo del usuario',
            'email.email' => 'El valor ingresado no es un correo valido.',
            'email.unique' => 'El correo ya se encuntra en uso.',
            'password.required'=>'Ingresa la contraseña del usuario',
            'password.string'=>'La contraseña del usuario contiene caracteres no validos.',
            'password.min'=>'Minimo 8 caracteres',
        ];

        $fields = Validator::make($data, [
            "name" => "required|string|max:100",
            "email" => "required|email|unique:users",
            "password" => "required|string|min:8",
           
        ], $messages );

        if($returnOnlyMessage){
            return $messages;
        }

        return $fields;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = User::find($request->id);

        if(!$user) response('usuario no encontrado' , 404);

        $fields = Validator::make($request->all(), [
            "name" => "required|string|max:100",
            "email" => "required|email",
        ], self::validateData([], true));

        if ($fields->fails()) {
            return response()->json($fields->errors(), 422);
        }

        if($request->email !== $user->email){
             // Validar que el email nuevo no esté registrado con otro usuario
            $validator = Validator::make(
                ['email' => $request->email],
                ['email' => 'unique:users,email,'.$user->id]
            );
        
            if ($validator->fails()) {
                // Agregar un mensaje de error al objeto de validación
                $validator->errors()->add('email', 'El email ya está registrado con otro usuario.');
        
                // Redirigir al usuario de vuelta al formulario de edición
                return response()->json($validator->errors(), 422);
            }

            $updatedata['email'] = $request->email;
        }else {
            $updatedata['email'] = $user->email;
        }

        $updatedata['name'] = $request->name;
        

        if($request->password && $request->password !== ''){
            $fields = Validator::make($request->all(), [
                "password" => "required|string|min:8",
            ], self::validateData([], true));
    
            if ($fields->fails()) {
                return response()->json($fields->errors(), 422);
            }

            $updatedata['password'] = bcrypt($request->password);
        }

        $user->update($updatedata);

        return response (true , 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($id){
            $user = User::find($id);
            if(!$user) return response( 'Usuario no encontrado' , 404) ;

            User::destroy($id);
        }else{
            return response( 'falta id del usuario para poder procesar la solicitud', 422);
        }

        return response (true , 200);
    }
}
