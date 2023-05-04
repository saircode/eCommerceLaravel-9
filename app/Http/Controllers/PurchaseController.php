<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\purchase;
use App\Models\ShoppingCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $purchases = purchase::where('user_id' , Auth::user()->id)
            ->latest()
            ->paginate(25);
        
        foreach ($purchases as $key => $item) {
            $item->products = DB::table('purchases_products')
                ->select('products.*')
                ->join('products' , 'products.id','purchases_products.product_id' )
                ->where('purchases_products.purchase_id', $item->id)
                ->get();
        }

        return Inertia::render('Purchases', [
            'purchases'=> $purchases
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
        $existPurchase = purchase::where('id_transaction',$request->id )->first();
        if($existPurchase) return response ( 'Compra ya registrada' , 200);

        $purchase = new purchase([
            'reference' => $request->reference,
            'id_transaction'=>$request->id,
            'user_id'=>Auth::user()->id,
            //->datos de envio:
            'address'=>$request->addres,
            'city'=>$request->city,
            'region'=>$request->region,
            'phone'=>$request->phonenumber,
            'payment_method'=>$request->payment_method['extra']['name'],
            'amount_in_cents'=>$request->amount_in_cents,
        ]);

        $purchase->save();

        $userId = Auth::user()->id;
        $cart = ShoppingCart::where('user_id', $userId)
        ->select('products.id as product_id', 'shopping_carts.id as cart_id')
        ->join('products', 'products.id', 'shopping_carts.product_id')
        ->latest('shopping_carts.created_at')
        ->get();

        foreach ($cart as $key => $productcart) {
            $product = Product::find($productcart->product_id);
            if($product){
                $product->stock = $product->stock - 1; // resto el stock
                DB::table('purchases_products')
                    ->insert([
                        'product_id' => $product->id,
                        'purchase_id' => $purchase->id,
                        'quantity' => 1
                    ]);
                
                ShoppingCartController::destroy($productcart->cart_id);
            }
        }


        return response ( true , 200);
    }

}
