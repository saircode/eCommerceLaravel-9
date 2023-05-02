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
}
