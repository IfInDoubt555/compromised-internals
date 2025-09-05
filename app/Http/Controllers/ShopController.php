<?php

namespace App\Http\Controllers;
use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class ShopController extends Controller
{
    public function index(): View
    {
        $products = Product::paginate(12);
        return view('shop.index', compact('products'));
    }
    public function show(Product $product): View
    {
        return view('shop.show', compact('product'));
    }
}