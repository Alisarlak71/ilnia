<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $products = Product::active()
            ->with(['variants', 'images'])
            ->latest('id')
            ->take(8)
            ->get();

        return view('home', ['products' => $products]);
    }
}
