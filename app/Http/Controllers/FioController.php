<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Jenssegers\Optimus\Optimus;
use App\Order;
use App\Fbcode;

class FioController extends Controller
{
    

   	// https://www.fio.cz/docs/cz/API_Bankovnictvi.pdf
   	public function dbg(Request $request, Optimus $optimus) {
	 	// $url = 'https://www.fio.cz/ib_api/rest/by-id/'.env('FIO_TOKEN').'/2020/1/transactions.json';
	 	// $url = 'https://www.fio.cz/ib_api/rest/last/'.env('FIO_TOKEN').'/transactions.json';

		// $transactions = $this->checkPayments($optimus); return response('checked', 200);
        // $this->setBreakpoint('32154689742'); return response('breaknuto', 200);
        // $transactions = $this->fetchFromToPayments(); return response()->json($transactions, 200);


	 	// return response($optimus->encode(2), 200); 
	 	// return response()->json(json_decode($this->debug_response()), 200);
	 	// $this->checkPayments();

   	}




   	/*
	 * Check for the new transactions and process them.
   	*/
   	public function checkPayments(Optimus $optimus) {
        $url = 'https://www.fio.cz/ib_api/rest/last/'.env('FIO_TOKEN').'/transactions.json';

	 // 	$ch = curl_init($url);
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);		// default to true...
		// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		// result as string (
		// $result = curl_exec($ch);
		// curl_close($ch);
		$result = $this->debug_response();						// DBG DBG DBG DBG

		$data = json_decode($result);
		$transactions = $data->accountStatement->transactionList->transaction ?? [];
		foreach ($transactions as $transaction) {
			$VS = $transaction->column5->value ?? NULL;
			$amount = $transaction->column1->value ?? -1;
			if(!is_numeric($VS) || $amount < 0)
				continue;
			$accName = $transaction->column10->value ?? 'Neznámej';
			$order = Order::whereId($optimus->decode($VS))->whereNull('paid_at')->where('cash_on_delivery', 0)->where('order_price', $amount)->first();
			if($order) {									// MATCH! Order has been successfully paid
				$order->paid_at = Carbon::now();
				$order->save();
				$fbadmin = Fbcode::where('code', '__ADMIN')->first();
				if($order->canceled_at !== NULL) {			// Ups! paid after deadline
					$fbadmin->sendMessage("$accName zacáloval PROŠLOU objednávku");
				} else {
					$fbadmin->sendMessage("$accName zacáloval objednávku");
				}

				Mail::to($order->email)->send(new OrderPaid($order, $optimus->encode($order->id)));
			}
		}
   	}

   	// DBG DBG DBG BDG DBG DBG DBG BDG DBG DBG DBG BDG DBG DBG DBG BDG DBG DBG DBG BDG DBG DBG DBG BDG
   	private function setBreakpoint($id) {
        $url = 'https://www.fio.cz/ib_api/rest/set-last-id/'.env('FIO_TOKEN').'/'.$id.'/';

	 	$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);		// default to true...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		// result as string 
		$result = curl_exec($ch);
		curl_close($ch);
   	}

   	/*
	 * Get JSON response from the bank on the request between two dates.
	 * Return all incoming and outgoing transactions as array.
   	*/
   	protected function fetchFromToPayments() {
        $from = '2020-08-01';
        $to = Carbon::now()->format('Y-m-d');
        $url = 'https://www.fio.cz/ib_api/rest/periods/'.env('FIO_TOKEN').'/'.$from.'/'.$to.'/transactions.json';

	 	$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);		// default to true...
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		// result as string 
		$result = curl_exec($ch);
		curl_close($ch);

		$data = json_decode($result);
		$transactions = $data->accountStatement->transactionList->transaction ?? [];
		$trns = [];
		foreach ($transactions as $key => $transaction) {
			$trns[$key]['amount'] 	= $transaction->column1->value;
			$trns[$key]['currency'] = $transaction->column14->value;
			$trns[$key]['VS'] 		= $transaction->column5->value ?? NULL;
			$trns[$key]['accName'] 	= $transaction->column10->value ?? 'Neznámej';
		}

		return $trns;
    }



   


}
