<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * pagina lista productos
     */
    public function index(){
        return Inertia::render('Products/Index');
    }
}
