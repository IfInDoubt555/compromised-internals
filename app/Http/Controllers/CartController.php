<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    public function index(): View
    {
        /** @var array<int, array{id:int,name:string,price:int,quantity:int,options:array{size?:string|null,color?:string|null}}> $cart */
        $cart = (array) session()->get('cart', []);

        return view(
            /** @var view-string $view */
            $view = 'shop.cart.index',
            compact('cart')
        );
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        /** @var array<int, array{id:int,name:string,price:int,quantity:int,options:array{size?:string|null,color?:string|null}}> $cart */
        $cart = (array) session()->get('cart', []);

        $size  = $request->input('size');
        $color = $request->input('color');

        if (!$product->has_variants) {
            $size = null;
            $color = null;
        }

        $id = (int) $product->id;

        $cart[$id] = [
            'id'       => $id,
            'name'     => (string) $product->name,
            'price'    => (int) $product->price,
            'quantity' => isset($cart[$id]) ? ((int) $cart[$id]['quantity']) + 1 : 1,
            'options'  => [
                'size'  => $size,
                'color' => $color,
            ],
        ];

        session()->put('cart', $cart);

        return redirect()
            ->route('shop.cart.index')
            ->with('success', "{$product->name} added to cart!");
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        /** @var array<int, array{id:int,name:string,price:int,quantity:int,options:array{size?:string|null,color?:string|null}}> $cart */
        $cart = (array) session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = (int) $validated['quantity'];
            session()->put('cart', $cart);
        }

        return redirect()->route('shop.cart.index');
    }

    public function remove(int $id): RedirectResponse
    {
        /** @var array<int, array{id:int,name:string,price:int,quantity:int,options:array{size?:string|null,color?:string|null}}> $cart */
        $cart = (array) session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('shop.cart.index');
    }
}