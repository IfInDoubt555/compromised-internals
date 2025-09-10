<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use app\Http\Requests\AddToCartRequest;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('shop.cart.index', compact('cart'));
    }

    public function add(AddToCartRequest $request, Product $product)
    {
        $data = $request->validated();

        $cart = session()->get('cart', []);

        // Never trust client price
        $cart[$product->id] = [
            'id'       => $product->id,
            'name'     => $product->name,
            'price'    => $product->price,   // from DB
            'quantity' => ($cart[$product->id]['quantity'] ?? 0) + $data['quantity'],
            'options'  => [
                'size'  => $product->has_variants ? $data['size'] ?? null : null,
                'color' => $product->has_variants ? $data['color'] ?? null : null,
            ],
        ];

        session()->put('cart', $cart);

        return redirect()->route('shop.cart.index')
            ->with('success', "{$product->name} added to cart!");
    }

    public function update(AddToCartRequest $request, $id)
    {
        $data = $request->validated();
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $data['quantity'];
            session()->put('cart', $cart);
        }

        return redirect()->route('shop.cart.index');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('shop.cart.index');
    }
}
