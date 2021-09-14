<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\Kratom;
use App\Rape;
use App\Cart;
use App\Order;
use App\Rating;
use App\Product;
use App\Picture;
use Session;

class CartController extends Controller
{
    
    /******************************************************
     * remove item from cart by product name. If cart doesn't exist, return 422. If item name doesn't exist, return 200 w/o changes.
     *****************************************************/
    public function deleteCartItem(Request $request) {
        // validate requests!!
        if(!Session::has('cart'))
            return response(['message' => 'Špatný požadavek.'], 422);
        $cart = Session::get('cart');
        $cart->removeByName($request->slug);   
        $request->session()->put('cart', $cart);
        $request->session()->save();
        if($cart->countItems() == 0)                                                
            return response('Redirecting', 200)                
                        ->header("AMP-Redirect-To", route('cart'))
                        ->header("Access-Control-Expose-Headers", 'AMP-Redirect-To');
        return response($cart->getItems(), 200);
    }

    public function addCartItem(Request $request) {
        $this->validate($request, [
                'produkt'    => ['required', 'string', 'max:255'],
                'nazev'      => ['required', 'string', 'max:255'],
                'mnozstvi'   => ['required', 'integer', 'min:1'],
            ]);

        $product = new Product($request->produkt, 0, $request->nazev);
        if(!$product->model) 
            return response(['message' => 'Produkt nenalezen.'], 422);

        $cart = Session::has('cart') ? Session::get('cart') : new Cart();
        $cart->add($product, $request->mnozstvi);
            // return response()->json(['message' => 'Na skladě máme pouze '.$product->model->order_max.' '.$product->model->unit_short], 422);
        $request->session()->put('cart', $cart);
        $request->session()->save();

        return response(['message' => 'Máš to tam'], 200);
    }

    public function updateCartItemsList(Request $request) {
        $this->validate($request, [
                'quantities'    => 'required|array',
                'quantities.*'  => 'required|integer'
            ]);
        if(!Session::has('cart'))
            return response(['message' => 'Chybí nákupní košík.'], 422);
        $cart = Session::get('cart');
        $cart->updateItemsQty($request->quantities);
        $request->session()->put('cart', $cart);
        $request->session()->save();
        return response($cart->getItems(), 200);                    // return new item list because of new price calculation for display
    }

    public function showCart(Request $request) {
        $cart = Session::has('cart') ? Session::get('cart') : new Cart();
        return response()->json($cart->getItems(), 200);
    }

    public function showMetaCart(Request $request) {
        $cart = Session::has('cart') ? Session::get('cart') : new Cart();
        return response()->json($cart->getMetaCartInfo(), 200);
    }

    public function cart() {
        $cart = Session::has('cart') ? Session::get('cart') : new Cart();
        return view('cart', compact('cart'));
    }
}
