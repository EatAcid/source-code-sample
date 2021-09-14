<?php

namespace App\Policies;

use App\User;
use App\Order;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function before(User $user) {
        if($user->hasRole('ADMIN')) {
            return true;
        } 
    }

    public function showDetail(?User $user, Order $order) {
        return ($order->user_id === NULL
                    || $order->user_id === optional($user)->id 
                    || optional($user)->hasRole('SKLADNIK')
                );
    }

    public function skladnik(User $user) {  
        return $user->hasRole('SKLADNIK');
    }


}
