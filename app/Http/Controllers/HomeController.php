<?php

namespace App\Http\Controllers;

use App\Models\Page;
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
            ->take(4)
            ->get();

        return view('home', [
            'products' => $products,
            // The hero's one hard fact comes from the panel's shipping settings, never from code.
            'shippingMethod' => $shipping->defaultMethod(),
            'aboutPage' => Page::published()->where('slug', 'about')->first(),
        ]);
    }
}
