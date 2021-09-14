<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Fbcode;
use App\Jobs\ProcessMessage;

use App\Order;

class MessengerController extends Controller
{
    // public function __construct() {
    //     // $this->middleware('messenger.token')->only('webhook');
    // }


   	public function dbg(Request $request) {
	 	// $sender  = $request['entry'][0]['messaging'][0]['sender']['id'];
   //      $message = $request['entry'][0]['messaging'][0]['message'];
	 	$sender  = '3615760348462629';
        $message = 'Xqo';
		// $this->sendTextMessage($sender, 'Hello! (normal)');
		ProcessMessage::dispatchAfterResponse($sender, $message);//->onQueue('messenger');

        return response('+', 200);
   	}



   	public function verify(Request $request) {
        if($request->get('hub_mode') === 'subscribe' && $request->get('hub_verify_token') === env('MESSENGER_VERIFY_TOKEN')) {
            return response($request->get('hub_challenge'), 200);
        } else {
            abort(404);
        }
   	}

    public function webhook(Request $request) {
    	if($request->filled(['entry.0.messaging.0.sender.id', 'entry.0.messaging.0.message.text'])) {
    		$sender = $request->input('entry.0.messaging.0.sender.id');
        	$message = $request->input('entry.0.messaging.0.message.text');				// validate MAX size???

        	ProcessMessage::dispatchAfterResponse($sender, $message);//->onQueue('messenger');
    	}

        return response('', 200);
    }


}
