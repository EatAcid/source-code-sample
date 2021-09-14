<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Order;
use Carbon\Carbon;

class ExpireOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    // public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->order->paid_at === null) {                                    // order still not paid
            if($this->order->created_at <= Carbon::now()->subHours(48)) {       // time passes - storno the order
                $this->order->storno();
            } else {                                                            // give time to user to pay
                // throw new \Exception('Not yet.');                            // stay job in the queue to call later
                ExpireOrder::dispatch($this->order)->delay(now()->addMinutes(5));
            }
        } 
        // order has been paid
    }
}
