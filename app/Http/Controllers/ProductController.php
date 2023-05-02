<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * pagina lista productos
     */
    public function index(){
        $allProducts = Product::latest()->get();

        return Inertia::render('Products/Index', [
            'allProducts'=>$allProducts
        ]);
    }

    /**
     * Registro los productos
     */
    public function store(Request $request){

        $data = json_decode($request->data , true) ;

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
