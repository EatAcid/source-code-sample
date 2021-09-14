<?php

namespace App\Http\Middleware;

use Closure;

class CheckCart
{
    /**
     * Handle an incoming request.
     * If user has not cart or has zero items in his cart. So we redirect him to his cart (can't go to order/payment page)
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->session()->has('cart') || $request->session()->get('cart')->countItems() === 0) {
            return redirect()->route('cart');
        }

        return $next($request);
    }
}
