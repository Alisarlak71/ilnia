<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

/**
 * A fixed content page (about / contact / terms / shipping). The set of pages is fixed in code
 * (SLUGS, routes); only title, body and visibility are edited in the panel.
 */
#[Fillable(['slug', 'title', 'body', 'meta_description', 'is_published'])]
class Page extends Model
{
    /** slug => route name */
    public const SLUGS = [
        'about' => 'pages.about',
        'contact' => 'pages.contact',
        'shipping' => 'pages.shipping',
        'terms' => 'pages.terms',
    ];

    protected $attributes = [
        'is_published' => false,
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function url(): string
    {
        return route(self::SLUGS[$this->slug]);
    }

    /** Markdown → HTML. Raw HTML typed in the panel is escaped, unsafe links are removed. */
    public function bodyHtml(): HtmlString
    {
        return new HtmlString(Str::markdown((string) $this->body, [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]));
    }
}
