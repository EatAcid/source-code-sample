<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Order;

class OrderCreated extends Mailable
{
    use Queueable, SerializesModels;
    public $order;
    public $hash;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order, $hash)
    {
        $this->order = $order;
        $this->hash = $hash;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Nová objednávka')
                    // ->view('emails.orders.created');
                    ->text('emails.orders.created_plain');
                    // ->attachData($this->pdf, 'name.pdf', [
                    //     'mime' => 'application/pdf',
                    // ]);
    }
}
