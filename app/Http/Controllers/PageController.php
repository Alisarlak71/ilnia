<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\ShippingCalculator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * A content page. Unpublished pages are 404 for everyone except admins, who see a
     * "draft" banner so they can preview the text before publishing it from the panel.
     */
    public function show(Request $request, string $slug, ShippingCalculator $shipping): View
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        $isAdmin = $request->user()?->is_admin === true;

        abort_unless($page->is_published || $isAdmin, 404);

        return view('pages.show', [
            'page' => $page,
            // The shipping page shows live cost / free-shipping rule from the panel settings.
            'shippingMethod' => $slug === 'shipping' ? $shipping->defaultMethod() : null,
        ]);
    }
}
