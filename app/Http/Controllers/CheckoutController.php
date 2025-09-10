<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function index(CheckoutRequest $request)
    {
        // Require auth for checkout (adjust to your needs)
        if (! Auth::check()) {
            return redirect()->route('login')->withErrors(['You must be logged in to checkout.']);
        }

        if (! config('payments.stripe.secret')) {
            Log::error('Stripe secret missing. Checkout unavailable.');
            return redirect()->route('errors.checkout-unavailable');
        }

        // Build a Stripe session from the server-side cart, NEVER client prices
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.cart.index')->withErrors(['Your cart is empty.']);
        }

        // Build line items using authoritative DB prices
        $lineItems = [];
        foreach ($cart as $row) {
            // $row: ['id','name','price','quantity','options'=>['size','color']]
            $product = Product::find($row['id']);
            if (! $product) {
                return redirect()->route('shop.cart.index')->withErrors(["An item in your cart is no longer available."]);
            }

            $lineItems[] = [
                'price_data' => [
                    'currency'     => 'usd',
                    'product_data' => ['name' => $product->name],
                    // Stripe requires amount in cents; cast to int
                    'unit_amount'  => (int) $product->price,
                ],
                'quantity' => (int) $row['quantity'],
            ];
        }

        Stripe::setApiKey(config('payments.stripe.secret'));

        $session = Session::create([
            'line_items'  => $lineItems,
            'mode'        => 'payment',
            'success_url' => route('checkout.success'),
            'cancel_url'  => route('checkout.cancel'),
            // You can set metadata here for reconciliation
            'metadata'    => [
                'user_id' => (string) Auth::id(),
            ],
        ]);

        return redirect($session->url);
    }

    public function success(CheckoutRequest $request)
    {
        // NOTE: In production you should verify payment via Stripe webhook.
        // This flow assumes success for demo/test purposes.

        $cart = session('cart', []);
        if (empty($cart) || ! Auth::check()) {
            return view('shop.checkout.success'); // idempotent render
        }

        // Recompute totals from DB to avoid tampering
        $totalCents = $this->calculateCartTotalFromDb($cart);

        $order = Order::create([
            'user_id'               => Auth::id(),
            'stripe_transaction_id' => 'TEST_' . strtoupper(Str::random(10)), // TODO: replace with real session/payment id
            'total_amount'          => $totalCents,
            'status'                => 'paid',
        ]);

        foreach ($cart as $item) {
            $product = Product::find($item['id']);
            if (! $product) {
                // Skip missing product; alternatively, you could fail the whole order.
                continue;
            }

            OrderItem::create([
                'order_id'     => $order->id,
                'product_name' => $product->name,
                'quantity'     => (int) $item['quantity'],
                'price'        => (int) $product->price, // authoritative
                'size'         => $product->has_variants ? ($item['options']['size']  ?? null) : null,
                'color'        => $product->has_variants ? ($item['options']['color'] ?? null) : null,
            ]);
        }

        session()->forget('cart');

        return view('shop.checkout.success');
    }

    public function cancel(CheckoutRequest $request)
    {
        // Keep cart for UX or clear – you chose to clear
        session()->forget('cart');

        return view('shop.checkout.cancel');
    }

    /**
     * Calculate total from authoritative DB prices (in cents).
     */
    protected function calculateCartTotalFromDb(array $cart): int
    {
        $total = 0;
        foreach ($cart as $item) {
            $product = Product::find($item['id']);
            if ($product) {
                $total += ((int) $product->price) * ((int) $item['quantity']);
            }
        }
        return $total;
    }
}