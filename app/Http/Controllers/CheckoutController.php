<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\OrderItem;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        if (empty(config('payments.stripe.secret'))) {
            Log::error('Stripe secret missing. Checkout unavailable.');
            return redirect()->route('errors.checkout-unavailable');
        }

        \Stripe\Stripe::setApiKey(config('payments.stripe.secret'));

        $session = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Compromised Internals Product',
                        ],
                        'unit_amount' => 499, // This is $4.99 in cents
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('checkout.success'), 
            'cancel_url' => route('checkout.cancel'),   
        ]);

        return redirect($session->url);
    }

    public function success()
    {
        if (session('cart')) {
            if (auth()->check()) {
                $order = Order::create([
                    'user_id' => auth()->id(),
                    'stripe_transaction_id' => 'TEST_' . strtoupper(Str::random(10)),
                    'total_amount' => $this->calculateCartTotal(),
                    'status' => 'paid',
                ]);

                foreach (session('cart') as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }
            }
            
            session()->forget('cart');
        }
    
        return view('shop.checkout.success');
    }

    protected function calculateCartTotal()
    {
        $total = 0;
        foreach (session('cart') as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function cancel()
    {
        session()->forget('cart');
        return view('shop.checkout.cancel');
    }
}
