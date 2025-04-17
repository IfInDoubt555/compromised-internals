<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    // Show Cart
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('shop.cart.index', compact('cart'));
    }

    // Add to Cart
    public function add(Product $product)
    {
        $cart = session()->get('cart', []);

        $cart[$product->id] = [
            "id" => $product->id,
            "name" => $product->name,
            "price" => $product->price,
            "quantity" => isset($cart[$product->id]) ? $cart[$product->id]['quantity'] + 1 : 1,
        ];

        session()->put('cart', $cart);

        return redirect()->route('shop.cart.index')->with('success', "{$product->name} added to cart!");
    }

    // Update Quantity
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);
    
        $cart = session()->get('cart', []);
    
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity; // <== just use $request
            session()->put('cart', $cart);
        }
    
        return redirect()->route('shop.cart.index');
    }
    

    // Remove Item
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
