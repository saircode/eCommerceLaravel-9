<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * pagina lista productos
     */
    public function index(Request $request){
        $allProducts = Product::latest()->paginate(25);

        return Inertia::render('Products/Index', [
            'allProducts'=>$allProducts
        ]);
    }

    /**
     * Registro los productos
     */
    public function store(Request $request){

        $data = json_decode($request->data , true) ;

        $fields = Validator::make($data, [
            "name" => "required|string|max:100",
            "stock" => "required|numeric",
            "description"=> 'string|max:250'
        ], [
            'name.required' => 'Ingresa el nombre del producto',
            'name.string' => 'El nombre del producto contiene caracteres no validos.',
            'name.max'=> 'Máximo 100 caracteres.',
            'stock.required' => 'Ingresa la cantidad existente del producto',
            'stock.numeric' => 'Este valor debe ser un número',
            'description.max'=> 'Máximo 250 caracteres.',
            'description.string' => 'Solo se permiten letras y números.'
        ]);

        //>> Respuesta con error por campo ausente o erroneo
        if ($fields->fails()) {
            return response()->json($fields->errors(), 422);
        }


        $product = new Product($data);

        if($request->image){
            $file = $request->file("image");
            $fileName   =  time() .'.'.$file->extension();
            $date =  date("Y-m-d");
            $folder = "app/public/uploads/images/" . $date. "/";

            if (!is_dir( storage_path( $folder) )) {
                mkdir( storage_path($folder) , 0777, true);
            }

            $file->move(storage_path($folder ), ($fileName) ); 

            $product->image = "/storage/uploads/images/$date/$fileName";
        }

        $product->save();

        return response(true, 200);
    }

    public function update(){}
    
    public function destroy(){}
}
