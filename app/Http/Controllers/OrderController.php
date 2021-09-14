<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderCreated;
use App\Mail\OrderPaid;
use App\Jobs\ExpireOrder;
use Jenssegers\Optimus\Optimus;
use Carbon\Carbon;
use App\Order;
use App\Order_item;
use App\Kratom;
use App\Cart;
use App\Fbcode;
use Session;


class OrderController extends Controller
{
    
    public function __construct() {
        $this->middleware('auth')->only('package', 'shipped');       
        // has cart && in cart items > 0
        $this->middleware('cart')->only('orderIndex', 'storeOrder', 'storeShipPay', 'chooseShipPay', 'orderForm');        
        $this->middleware('throttle:10,1')->only('storeOrder');             // limits to 10 requests per 1 minute for one IP
    }


    public function chooseShipPay() {
        return view('orders.shippingType_paymentMethod');
    }

    public function storeShipPay(Request $request) {
        $this->validate($request, [
                'zpusob_platby'     => ['required', 'string', 'in:prevod,dobirka'],
                'dopravce'          => ['required', 'string', 'in:zasilkovna,doruceni_cz,doruceni_sk'],
                'zasilkovna_id'     => ['required', 'numeric'],    
                'depo_adresa'       => ['nullable', 'required_if:dopravce,zasilkovna', 'string', 'max:255'], 
            ]);

        // javascript ze zasilkovny rozbiji amp bind, musime osetrit jeste tady...
        if($request->dopravce === 'zasilkovna') {
            if($request->zasilkovna_id === '106' || $request->zasilkovna_id === '131')  // doruceni_cz, doruceni_sk
                return response()->json(['message' => 'Stala se chyba, zkus znovu vybrat místo depa zásilkovny'], 422);
            $depo_address = $request->depo_adresa;   
        } else {                                                                     
            $depo_address = '';         // kdyz da zasilkovnu a pak zmeni na dopravce, adresa nekdy zustava, musime to vymazat
        }

        
        // save property to cart, chage and move
        $cart = Session::get('cart');
        $cart->payment_method = $request->zpusob_platby;
        // $cart->carrier = $request->dopravce;
        $cart->zasilkovna_id = $request->zasilkovna_id;
        $cart->depo_address = $depo_address;

        $request->session()->put('cart', $cart);
        $request->session()->save();

        // return response()->json(['message' => $request->zasilkovna_id], 200);
        return response(['message' => 'Redirecting'], 200)
                        ->header("AMP-Redirect-To", route('orderIndex'))
                        ->header("Access-Control-Expose-Headers", 'AMP-Redirect-To');
    }
    
    public function orderForm() {
        $cart = Session::get('cart');
        return view('orders.userform', compact('cart'));
    }

    public function orderPay($hash, Optimus $optimus) {
        $order = Order::whereId($optimus->decode($hash))->whereNull('paid_at')->whereNull('canceled_at')->firstOrFail();
        return view('orders.payment', compact('order', 'hash'));
    }

    public function orderFinished() {
        return view('orders.finished');
    }

    public function ordersList(Optimus $optimus) {                                    // BY AMP LIST?
        $orders = Order::where('user_id', Auth::id())->whereNull('canceled_at')->orderBy('created_at', 'desc')->get();
        foreach ($orders as $key => $order) {
            $hashs[$key] = $optimus->encode($order->id);
        }
        return view('orders.list', compact('orders', 'hashs'));
    }

    public function orderDetail($hash, Optimus $optimus) {
        if(!is_numeric($hash))
            abort(404);
        $order = Order::whereId($optimus->decode($hash))->firstOrFail();
        $this->authorize('showDetail', $order);                             // 403 
        return view('orders.detail', compact('order', 'hash'));
    }
    

    public function storeOrder(Request $request, Optimus $optimus) {
        $this->validate($request, [
                'first_name'        => ['required', 'string', 'max:255'],
                'last_name'         => ['required', 'string', 'max:255'],
                'email'             => ['required', 'string', 'email', 'max:255'],
                'telephone'         => ['required', 'regex:/^[0-9-+s()\s]*$/'],
                'depo'              => ['nullable'],
                'country'           => ['nullable', 'required_without:depo', 'string', 'in:cz,sk'],
                'street'            => ['nullable', 'required_without:depo', 'string', 'max:255'],
                'house_number'      => ['nullable', 'required_without:depo', 'integer', 'min:1'],
                'city'              => ['nullable', 'required_without:depo', 'string', 'max:255'],
                'post_code'         => ['nullable', 'required_without:depo', 'regex:/^(\d{5})*$/'],
                'user_note'         => ['nullable', 'string', 'max:65534'],
                'billing_info'      => ['nullable'],
                'billing_name'      => ['nullable', 'required_without:billing_info', 'string', 'max:255'],
                'billing_country'   => ['nullable', 'required_without:billing_info', 'string', 'max:255'],
                'billing_street'    => ['nullable', 'required_without:billing_info', 'string', 'max:255'],
                'billing_city'      => ['nullable', 'required_without:billing_info', 'string', 'max:255'],
                'billing_post_code' => ['nullable', 'required_without:billing_info', 'regex:/^(\d{5})*$/'],
                'billing_IC'        => ['nullable', 'required_without:billing_info', 'string', 'max:255'],
                'billing_DIC'       => ['nullable', 'required_without:billing_info', 'string', 'max:255'],
            ]);

        $user_id = Auth::check() ? Auth::id() : NULL;
        $cart = Session::get('cart');
        $total_price = $cart->countTotalPrice();

        $order = Order::create([
                    'user_id'           => $user_id,
                    'first_name'        => $request->first_name,
                    'last_name'         => $request->last_name,
                    'email'             => $request->email,
                    'telephone'         => $request->telephone,
                    'country'           => $request->country,
                    'zasilkovna_id'     => $cart->zasilkovna_id,   // Branch ID or an ID of the external carrier.
                    'street'            => $request->street,
                    'house_number'      => $request->house_number,
                    'city'              => $request->city,
                    'post_code'         => $request->post_code,
                    'user_note'         => $request->user_note,
                    'billing_name'      => $request->billing_name,
                    'billing_country'   => $request->billing_country,
                    'billing_street'    => $request->billing_street,
                    'billing_city'      => $request->billing_city,
                    'billing_post_code' => $request->billing_post_code,
                    'billing_IC'        => $request->billing_IC,
                    'billing_DIC'       => $request->billing_DIC,
                    'order_price'       => $total_price,
                    'cash_on_delivery'  => $cart->payment_method === 'dobirkou' ? 1 : 0,
                ]);

        $stockSumOK = 1;
        foreach ($cart->products as $product) {
            if(!$product->takeFromStock($product->quantity)) {                                                       
                $stockSumOK = 0;
                $failedProduct = $product;
            }                                       
        	$order_item = Order_item::create([
                    'order_id'          => $order->id,
                    'product_id'        => $product->model->id,
                    'model_name'        => $product->namespace,
                    'quantity'          => $product->quantity,
                ]);
        }

        if(!$stockSumOK) {                                                  
            $order->storno();
            return response(['message' => 'Než jsi to doklikal, tak '.$failedProduct->name.' už někdo bohůžel vykoupil :( Musíš ho vzít méně.',
                             'order_m' => 1], 422);
        }


        // gen zasilkovna bar code
        $data = array(
                    'number'    => $optimus->encode($order->id),
                    'name'      => $order->first_name,
                    'surname'   => $order->first_name,
                    'email'     => $order->email,
                    'phone'     => $order->telephone,
                    'addressId' => $cart->zasilkovna_id,            // Branch ID or an ID of the external carrier.
                    'value'     => $order->order_price,
                    // 'weight'    => $order->weight_kg,
                    // 'size'           => [length, width, height] mm
                    // 'company'   => $order->billing_name,
            );
        if(!isset($request->depo)) {
            $data['street']      = $order->street;
            $data['houseNumber'] = $order->house_number;
            $data['city']        = $order->city;
            $data['zip']         = $order->post_code;
        }



        $request->session()->forget('cart');
        $request->session()->save();

        if(!$order->cash_on_delivery) {
            ExpireOrder::dispatch($order);
        }
        Mail::to($order->email)->send(new OrderCreated($order, $optimus->encode($order->id)));

        return response(['message' => 'Redirecting'], 200)
        				->header("AMP-Redirect-To", route('orderPay', ['hash' => $optimus->encode($order->id)]))
                        ->header("Access-Control-Expose-Headers", 'AMP-Redirect-To');
    }


    public function shipped(Request $request, Optimus $optimus) {
        $this->authorize('skladnik', Order::class);               // users with role SKLADNIK or ADMIN (see Policy)
        $this->validate($request, [
                'hash' => ['required', 'integer'],
            ]);

        $order = Order::whereId($optimus->decode($request->hash))->whereNull('shipped_at')->first();
        if(!$order)
            return response()->json(['message' => 'Tahle objednávka nelze vyřídit'], 422);
        $order->shipped_at = Carbon::now();
        $order->save();
        
        // $fbcode = $order->user->fbcode ?? NULL;         // order couldn't have user_id (= fbcode)
        // if($fbcode)                                     // if not active condition in the fun
        //     $fbcode->sendMessage("Čus virus. Zrovna jsem ti odeslala balíček prostřednictvím zásilkovny, máš ho na cestě :)");

        // Mail::to($order->email)->send(new OrderPaid($order, $optimus->encode($order->id)));
        
        return response()->json(['message' => 'Vyřízeno'], 200);
    }

    public function package(Optimus $optimus) {
        $this->authorize('skladnik', Order::class);               // users with role SKLADNIK or ADMIN (see Policy)
        $orders = Order::whereNull('shipped_at')
                            ->where(function ($query) {
                                   $query->where('cash_on_delivery', 1)
                                         ->orWhereNotNull('paid_at');
                               })
                            ->orderBy('created_at')->get();

        foreach ($orders as $key => $order) {
            $hashs[$key] = $optimus->encode($order->id);
        }
        return view('orders.package', compact('orders', 'hashs'));
    }


    public function zasilkovnaSOAP(Optimus $optimus) {
        $order = Order::find(1);
        $data = array(
                    'number' => $optimus->encode($order->id),
                    'name' => "Petr",
                    'surname' => "Novák",
                    'email' => "petr@novak.cz",
                    'phone' => "+420777123456",
                    'addressId' => 79,
                    'cod' => 145,
                    'value' => 145.55,
                    'eshop' => "muj-eshop.cz"
            );

        // $xml = $this->packeteryXML($data, 'createPacket');
        $xml = $this->packeteryXML($data, 'packetAttributesValid');
        $result = '<?xml version="1.0" encoding="utf-8"?>
        <response><status>ok</status><result><id>1057646138</id><barcode>Z1057646138</barcode><barcodeText>Z 105 7646 138</barcodeText></result></response>';
        // $result = $this->RESTAPI($xml);
        if(!isset($result->status) || $result->status !== 'ok') {
            // error!
            return response()->json($result, 400);
        }

        return response()->json($result, 200);

    }

    // trash deprecate
    public function arrayToXml($array, $rootElement = null, $xml = null) { 
        $_xml = $xml; 
        // If there is no Root Element then insert root 
        if ($_xml === null) { 
            $_xml = new \SimpleXMLElement($rootElement); 
        } 
        // Visit all key value pair 
        foreach ($array as $k => $v) { 
            // If there is nested array then 
            if (is_array($v)) {  
                // Call function for nested array 
                $this->arrayToXml($v, $k, $_xml->addChild($k)); 
                } 
            else { 
                // Simply add child element.  
                $_xml->addChild($k, $v); 
            } 
        } 
        return $_xml->asXML(); 
    } 

    /* create XML from array data */
    public function packeteryXML($array,  $fun) {
        $_xml = new \SimpleXMLElement('<'.$fun.'/>'); 
        
        $_xml->addChild('apiPassword', env('ZASILKOVNA_PASS')); 
        $packetAttributes = $_xml->addChild('packetAttributes');
        foreach ($array as $k => $v) { 
            $packetAttributes->addChild($k, $v); 
        } 
        $packetAttributes->addChild('eshop', 'DobrejMatros.com');
        $packetAttributes->addChild('currency', 'CZK');
        
        return $_xml->asXML(); 
    }

    /* send POST xml to zasilkovna to create barcode */
    public function RESTAPI($xml) {
        $ch = curl_init('https://www.zasilkovna.cz/api/rest');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $result = curl_exec($ch);
        curl_close($ch);

        // parse result, return php object
        $xml = simplexml_load_string($result);
        $json = json_encode($xml);
        return json_decode($json);
    }

}
