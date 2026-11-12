<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    private function product(array $attributes = []): Product
    {
        $category = Category::factory()->create(['name' => 'عسل', 'slug' => 'honey']);
        $product = Product::factory()->for($category)->create($attributes + [
            'name' => 'عسل طبیعی نمونه', 'slug' => 'natural-honey', 'is_active' => true,
            'short_description' => 'عسل طبیعی از کوه‌های زاگرس.',
        ]);
        ProductVariant::factory()->for($product)->create(['title' => '۱ کیلوگرم', 'price' => 480000, 'stock' => 3, 'is_active' => true]);

        return $product;
    }

    public function test_home_has_description_canonical_and_open_graph(): void
    {
        $this->get('/')
            ->assertSee('<title>ایلنیا | به رسم نیاکان</title>', false)
            ->assertSee('<meta name="description" content="ایلنیا؛ عسل طبیعی و محصولات اصیل', false)
            ->assertSee('<link rel="canonical" href="'.url('/').'">', false)
            ->assertSee('<meta property="og:locale" content="fa_IR">', false)
            ->assertSee('<meta property="og:image" content="'.asset('brand/profile-mark.png').'">', false)
            ->assertDontSee('noindex');
    }

    public function test_product_page_has_own_meta_and_structured_data(): void
    {
        $product = $this->product();

        $response = $this->get(route('products.show', $product))
            ->assertSee('<meta name="description" content="عسل طبیعی از کوه‌های زاگرس.">', false)
            ->assertSee('<meta property="og:type" content="product">', false)
            ->assertSee('<link rel="canonical" href="'.route('products.show', $product).'">', false)
            ->assertSee('application/ld+json', false);

        preg_match('#<script type="application/ld\+json">(.+?)</script>#s', $response->getContent(), $m);
        $data = json_decode($m[1], true);

        $this->assertSame('Product', $data['@type']);
        $this->assertSame('عسل طبیعی نمونه', $data['name']);
        $this->assertSame(4800000, $data['offers'][0]['price']); // Rial
        $this->assertSame('IRR', $data['offers'][0]['priceCurrency']);
        $this->assertSame('https://schema.org/InStock', $data['offers'][0]['availability']);
        $this->assertArrayNotHasKey('aggregateRating', $data);
        $this->assertArrayNotHasKey('review', $data);
    }

    public function test_product_list_canonical_keeps_category_only(): void
    {
        $this->product();

        $this->get('/products?category=honey&utm_source=instagram')
            ->assertSee('<link rel="canonical" href="'.route('products.index', ['category' => 'honey']).'">', false);
    }

    public function test_private_pages_are_noindex(): void
    {
        $this->get('/cart')->assertSee('<meta name="robots" content="noindex, nofollow">', false);
        $this->get('/login')->assertSee('<meta name="robots" content="noindex, nofollow">', false);
        $this->actingAs(User::create(['mobile' => '09121234567']))
            ->get('/account/orders')->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_draft_page_preview_is_noindex(): void
    {
        $this->seed(PageSeeder::class);

        $this->actingAs(User::factory()->admin()->create())
            ->get('/about')->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_sitemap_lists_only_public_pages(): void
    {
        $this->seed(PageSeeder::class);
        Page::where('slug', 'contact')->update(['is_published' => true]);
        $active = $this->product();
        $hidden = Product::factory()->for($active->category)->create(['slug' => 'hidden-honey', 'is_active' => false]);

        $response = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $xml = simplexml_load_string($response->getContent());
        $locs = [];
        foreach ($xml->url as $url) {
            $locs[] = (string) $url->loc;
        }

        $this->assertContains(route('home'), $locs);
        $this->assertContains(route('products.index'), $locs);
        $this->assertContains(route('products.index', ['category' => 'honey']), $locs);
        $this->assertContains(route('products.show', $active), $locs);
        $this->assertContains(route('pages.contact'), $locs);
        $this->assertNotContains(route('products.show', $hidden), $locs);
        $this->assertNotContains(route('pages.about'), $locs);
    }

    public function test_robots_blocks_everything_outside_production(): void
    {
        $this->get('/robots.txt')->assertOk()->assertSee("User-agent: *\nDisallow: /", false)->assertDontSee('Sitemap');
    }

    public function test_robots_in_production_blocks_private_pages_and_announces_sitemap(): void
    {
        $this->app['env'] = 'production';

        $this->get('/robots.txt')
            ->assertSee('Disallow: /admin')
            ->assertSee('Disallow: /checkout')
            ->assertSee('Disallow: /account')
            ->assertSee('Sitemap: '.route('sitemap'));
    }
}
