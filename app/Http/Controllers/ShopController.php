<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShopController extends Controller
{
    public function index(){
        $allProducts = Product::latest()->paginate(25);
        return Inertia::render('Shop' , [
            "allProducts" => $allProducts
        ]);
    }
}
