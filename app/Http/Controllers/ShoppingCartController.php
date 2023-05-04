<?php

namespace App\Http\Controllers;

use App\Models\ShoppingCart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ShoppingCartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = Auth::user()->id;
        $cart = ShoppingCart::where('user_id', $userId)
        ->select('products.image', 'products.name', 'products.price', 'shopping_carts.*')
        ->join('products', 'products.id', 'shopping_carts.product_id')
        ->latest('shopping_carts.created_at')
        ->get();

        $total = 0;
        foreach ($cart as $key => $p) {
            $total += $p->price;
        }

        return response ([
            'cart'=> $cart,
            'total'=>$total
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = Validator::make($request->all(), [
            "product_id" => "required|numeric",
            "quantity" => "required|numeric"
        ]);

        if ($fields->fails()) {
            return response()->json($fields->errors(), 422);
        }

        $shoppingCart = new ShoppingCart ($request->all());
        $shoppingCart->user_id = Auth::user()->id;
        $shoppingCart->save();

        return response (true ,  200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ShoppingCart  $shoppingCart
     * @return \Illuminate\Http\Response
     */
    public function show(ShoppingCart $shoppingCart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ShoppingCart  $shoppingCart
     * @return \Illuminate\Http\Response
     */
    public function edit(ShoppingCart $shoppingCart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShoppingCart  $shoppingCart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShoppingCart $shoppingCart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShoppingCart  $shoppingCart
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($id){
            $shoppingcart = ShoppingCart::find($id);
            if(!$shoppingcart) return response( 'no encontrado' , 404) ;

            ShoppingCart::destroy($id);
        }else{
            return response( 'falta id del carrito para poder procesar la solicitud', 422);
        }

        return response (true , 200);
    }
}
