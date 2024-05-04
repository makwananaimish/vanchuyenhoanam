<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if ($guard == 'customer') {
                    $id = Auth::guard($guard)->id();
                    $routeName = $request->route()->getName();

                    if ($routeName === 'user.change_password') {
                        return $next($request);
                    }

                    if (
                        $routeName === 'transaction.index' ||
                        $routeName === 'transaction.create' ||
                        $routeName === 'transaction.customer.create' ||
                        $routeName === 'transaction.show' ||
                        $routeName === 'transaction.accept' ||
                        $routeName === 'transaction.cancel' ||
                        $routeName === 'payable.orders' ||
                        $routeName === 'payable.pay' ||
                        $routeName === 'payable.debt' ||
                        $routeName === 'order_declaration.create' ||
                        $routeName === 'order_declaration.delete' ||
                        $routeName === 'message.get' ||
                        $routeName === 'message.create'
                    ) {
                        return $next($request);
                    }

                    if (
                        $routeName === 'customer.orders' ||
                        $routeName === 'customer.orders.completed' ||
                        $routeName === 'customer.orders.notify_address'
                    ) {
                        if ($request->route()->parameter('customer')->id === $id) {
                            return $next($request);
                        }
                    }

                    if ($routeName === 'order.show') {
                        $order = $request->route()->parameter('order');

                        if ($order->customer_id === $id) {
                            return $next($request);
                        }
                    }

                    return redirect()->route('customer.orders', ['customer' => $id]);
                }
            }
        }

        return $next($request);
    }
}
