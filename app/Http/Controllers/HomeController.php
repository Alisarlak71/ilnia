<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ShippingCalculator;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(ShippingCalculator $shipping): View
    {
        $products = Product::active()
            ->with(['variants', 'images'])
            ->latest('id')
            ->take(8)
            ->get();

        return view('home', [
            'products' => $products,
            // The hero's free-shipping line comes from the panel's shipping settings.
            'shippingMethod' => $shipping->defaultMethod(),
        ]);
    }
}
