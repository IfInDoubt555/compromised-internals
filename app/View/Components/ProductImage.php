<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ProductImage extends Component
{
    public $image;
    public $colors;

    public function __construct($image, $colors = [])
    {
        $this->image = $image;
        $this->colors = $colors;
    }

    public function render()
    {
        return view('components.product-image');
    }
}