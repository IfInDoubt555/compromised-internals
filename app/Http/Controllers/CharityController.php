<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class CharityController extends Controller
{
    public function index(): View
    {
        return view(
            /** @var view-string $view */
            $view = 'charity.index'
        );
    }
}