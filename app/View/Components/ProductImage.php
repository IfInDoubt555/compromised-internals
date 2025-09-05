<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductImage extends Component
{
    public ?string $image;

    /** @var array<string,string> */
    public array $colors;

    /**
     * @param array<string,string>|null $colors
     */
    public function __construct(?string $image = null, ?array $colors = null)
    {
        $this->image  = $image;
        $this->colors = $colors ?? [];
    }

    public function render(): View|string
    {
        return view('components.product-image');
    }
}