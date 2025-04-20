<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\OrderItem;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        if (!config('payments.stripe.secret')) {
            Log::error('Stripe secret missing. Checkout unavailable.');
            return redirect()->route('errors.checkout-unavailable');
        }

        Stripe::setApiKey(config('payments.stripe.secret'));

        $session = Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Compromised Internals Product',
                    ],
                    'unit_amount' => 499,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('checkout.success'),
            'cancel_url' => route('checkout.cancel'),
        ]);

        return redirect($session->url);
    }

    public function success()
    {
        $cart = session('cart');

        if (!empty($cart) && Auth::check()) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'stripe_transaction_id' => 'TEST_' . strtoupper(Str::random(10)),
                'total_amount' => $this->calculateCartTotal($cart),
                'status' => 'paid',
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'size' => $item['options']['size'] ?? null,
                    'color' => $item['options']['color'] ?? null,
                ]);
            }

            session()->forget('cart');
        }

        return view('shop.checkout.success');
    }

    protected function calculateCartTotal(array $cart): int
    {
        return array_reduce($cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);
    }

    public function cancel()
    {
        session()->forget('cart');
        return view('shop.checkout.cancel');
    }
}
