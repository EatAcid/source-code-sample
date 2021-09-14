<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Fbcode;
use Carbon\Carbon;

class ProcessMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sender;      // facebook ID of the sender
    protected $message;     // raw message text


    public function __construct($sender, $message)
    {
        $this->sender = $sender;
        $this->message = $message;
    }

 
    public function handle()
    {
        if(strlen($this->message) <= 10) {                                            // we got fb code! 
            $fbcode = Fbcode::where('code', $this->message)->whereNull('used_at')->latest()->first();  
            if($fbcode) {
                $fbcode->used_at = Carbon::now();
                $fbcode->messenger_id = $this->sender;
                $fbcode->save();      
                $fbcode->sendMessage('Potvrzuji správnej kód (y)');          
            } else {
                // "Ups, tenhle kód nerozpoznávám :/"
            }
        } else {
            // 'něco, co má divný počet znaků...'
        }
    }



}
