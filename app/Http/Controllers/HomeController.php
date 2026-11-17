<?php

namespace App\Http\Controllers;

use App\Models\Category;
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

        // Category shortcuts: only categories that have at least one active product.
        $categories = Category::query()
            ->whereHas('products', fn ($query) => $query->active())
            ->withCount(['products' => fn ($query) => $query->active()])
            ->ordered()
            ->get();

        return view('home', [
            'products' => $products,
            'categories' => $categories,
            // The hero's free-shipping line comes from the panel's shipping settings.
            'shippingMethod' => $shipping->defaultMethod(),
        ]);
    }
}
