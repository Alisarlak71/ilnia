<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Http\Response;

/**
 * sitemap.xml and robots.txt, generated from the database (no package needed).
 * Only public, indexable pages go into the sitemap: home, product list, categories,
 * active products and published content pages.
 */
class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect([
            ['loc' => route('home'), 'lastmod' => null],
            ['loc' => route('products.index'), 'lastmod' => Product::active()->max('updated_at')],
        ]);

        Category::query()
            ->whereHas('products', fn ($query) => $query->active())
            ->ordered()
            ->get()
            ->each(fn (Category $category) => $urls->push([
                'loc' => route('products.index', ['category' => $category->slug]),
                'lastmod' => null,
            ]));

        Product::active()->latest('id')->get(['id', 'slug', 'updated_at'])
            ->each(fn (Product $product) => $urls->push([
                'loc' => route('products.show', $product),
                'lastmod' => $product->updated_at,
            ]));

        Page::published()->get()
            ->each(fn (Page $page) => $urls->push(['loc' => $page->url(), 'lastmod' => $page->updated_at]));

        return response()
            ->view('seo.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * Outside production (local, staging…) everything is blocked, so a test copy of the site is
     * never indexed. In production: private pages are blocked and the sitemap is announced.
     */
    public function robots(): Response
    {
        $lines = app()->isProduction()
            ? [
                'User-agent: *',
                'Disallow: /admin',
                'Disallow: /cart',
                'Disallow: /checkout',
                'Disallow: /login',
                'Disallow: /account',
                'Disallow: /payment',
                'Disallow: /livewire',
                '',
                'Sitemap: '.route('sitemap'),
            ]
            : ['User-agent: *', 'Disallow: /'];

        return response(implode("\n", $lines)."\n")->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
