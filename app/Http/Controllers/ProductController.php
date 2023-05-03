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
        $validate = self::validateData($request->all());
        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $product = new Product($request->all());
        $product->save();

        return response([
            "product_id" => $product->id
        ], 200);
    }

    public function storeImg(Request $request) {
        if($request->image && $request->product_id){
            $product = Product::find($request->product_id);

            $file = $request->file("image");

            //-> si se envio la variable pero no con un archivo 
            if(!$file) return response ('no se encontro la imagen' , 200);

            $fileName   =  time() .'.'.$file->extension();
            $date =  date("Y-m-d");
            $folder = "app/public/uploads/images/" . $date. "/";

            if (!is_dir( storage_path( $folder) )) {
                mkdir( storage_path($folder) , 0777, true);
            }

            $file->move(storage_path($folder ), ($fileName) ); 

            $product->image = "/storage/uploads/images/$date/$fileName";
            $product->save();
        }
    }

    public function update(Request $request){
        $validate = self::validateData($request->all());
        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        $product = Product::find($request->id);
        if(!$product){
            return response ('Producto no encontrado.' , 404);
        }

        $product->update($request->all());

        return response(['product_id'=>$product->id],  200);
    }

    /**
     * Valida campos obligatorios y sus tipos
     */
    static public function validateData($data) {
        
        $fields = Validator::make($data, [
            "name" => "required|string|max:100",
            "stock" => "required|numeric",
            "description"=> 'string|max:250',
            "price" =>"required|numeric",
        ], [
            'name.required' => 'Ingresa el nombre del producto',
            'name.string' => 'El nombre del producto contiene caracteres no validos.',
            'name.max'=> 'Máximo 100 caracteres.',
            'stock.required' => 'Ingresa la cantidad existente del producto',
            'stock.numeric' => 'Este valor debe ser un número',
            'description.max'=> 'Máximo 250 caracteres.',
            'description.string' => 'Solo se permiten letras y números.',
            'price.required' => 'Ingresa el precio de venta del producto',
            'price.numeric' => 'Este valor debe ser un número',
        ]);

        return $fields;
    }
    
    public function destroy($id){
        if($id){
            $product = Product::find($id);
            if(!$product) return response( 'Producto no encontrado' , 404) ;

            Product::destroy($id);
        }else{
            return response( 'falta id del producto para poder procesar la solicitur', 422);
        }

        return response (true , 200);
    }
}
