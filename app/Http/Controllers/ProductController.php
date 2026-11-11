<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /** Product list, optionally filtered by category: /products?category=عسل */
    public function index(Request $request): View
    {
        // Only categories that have at least one active product are offered as filters.
        $categories = Category::query()
            ->whereHas('products', fn ($query) => $query->active())
            ->ordered()
            ->get();

        $category = null;

        if ($request->filled('category')) {
            $category = $categories->firstWhere('slug', (string) $request->query('category'));
            abort_if($category === null, 404);
        }

        $products = Product::active()
            ->with(['variants', 'images'])
            ->when($category, fn ($query) => $query->whereBelongsTo($category))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'category' => $category,
        ]);
    }
}
