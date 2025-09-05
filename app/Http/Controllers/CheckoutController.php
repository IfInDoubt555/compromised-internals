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
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;

class CheckoutController extends Controller
{
    public function index(Request $request): RedirectResponse
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

        /** @var RedirectResponse */
        return redirect($session->url);
    }

    public function success(): View
    {
        /** @var array<int, array{ name:string, quantity:int, price:int, options?:array<string,mixed> }> $cart */
        $cart = (array) session('cart', []);
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
                    'quantity' => (int) $item['quantity'],
                    'price' => (int) $item['price'],
                    'size' => $item['options']['size'] ?? null,
                    'color' => $item['options']['color'] ?? null,
                ]);
            }

            session()->forget('cart');
        }

        return view(
                    /** @var view-string $view */
                    $view = 'shop.checkout.success'
        );    
    }

    /**
     * @param array<int, array{price:int, quantity:int}> $cart
     */
    protected function calculateCartTotal(array $cart): int
    {
        /** @var int $total */
        $total = array_reduce(
            $cart,
            /** @param array{price:int, quantity:int} $item */
            function (int $carry, array $item): int {
                return $carry + ((int) $item['price'] * (int) $item['quantity']);
            },
            0
        );
        return $total;
    }

    public function cancel(): View
    {
        session()->forget('cart');
        return view(
            /** @var view-string $view */
            $view = 'shop.checkout.cancel'
        );    
    }
}
