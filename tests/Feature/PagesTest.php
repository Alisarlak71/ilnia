<?php

namespace Tests\Feature;

use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Models\Page;
use App\Models\ShippingMethod;
use App\Models\User;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PageSeeder::class);
    }

    private function publish(string $slug, array $attributes = []): Page
    {
        $page = Page::where('slug', $slug)->sole();
        $page->update($attributes + ['is_published' => true]);

        return $page;
    }

    public function test_seeder_creates_four_unpublished_drafts_and_keeps_edits(): void
    {
        $this->assertSame(['about', 'contact', 'shipping', 'terms'], Page::orderBy('slug')->pluck('slug')->sort()->values()->all());
        $this->assertSame(0, Page::published()->count());

        Page::where('slug', 'about')->update(['title' => 'عنوان ویرایش‌شده']);
        $this->seed(PageSeeder::class);

        $this->assertSame('عنوان ویرایش‌شده', Page::where('slug', 'about')->value('title'));
        $this->assertSame(4, Page::count());
    }

    public function test_unpublished_page_is_404_for_visitors_and_customers(): void
    {
        $this->get('/about')->assertNotFound();
        $this->actingAs(User::create(['mobile' => '09121234567']))->get('/terms')->assertNotFound();
    }

    public function test_admin_can_preview_a_draft(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/contact')
            ->assertOk()
            ->assertSee('پیش‌نویس')
            ->assertSee('[شماره تلفن]');
    }

    public function test_published_page_renders_markdown_and_meta_description(): void
    {
        $this->publish('about', ['body' => "## تیتر\n\n- **اصالت**", 'meta_description' => 'توضیح نمونه']);

        $this->get('/about')
            ->assertOk()
            ->assertSee('<title>دربارهٔ ایلنیا | ایلنیا</title>', false)
            ->assertSee('<meta name="description" content="توضیح نمونه">', false)
            ->assertSee('<h2>تیتر</h2>', false)
            ->assertSee('<strong>اصالت</strong>', false)
            ->assertDontSee('پیش‌نویس');
    }

    public function test_html_and_javascript_links_in_body_are_not_executed(): void
    {
        $this->publish('terms', ['body' => "<script>alert(1)</script>\n\n[x](javascript:alert(1))"]);

        $this->get('/terms')
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('javascript:alert', false);
    }

    public function test_shipping_page_shows_live_cost_from_panel(): void
    {
        ShippingMethod::factory()->create(['name' => 'پست', 'cost' => 60000, 'free_over_weight_grams' => 4000]);
        $this->publish('shipping');

        $this->get('/shipping')
            ->assertSee('هزینهٔ ارسال با پست')
            ->assertSee('۶۰٬۰۰۰ تومان')
            ->assertSee('برای سفارش‌های ۴ کیلوگرم و بیشتر');
    }

    public function test_footer_links_only_published_pages(): void
    {
        $this->publish('contact');

        $this->get('/')
            ->assertSee(route('pages.contact'))
            ->assertDontSee(route('pages.about'))
            ->assertDontSee(route('pages.terms'));
    }

    public function test_admin_edits_and_publishes_a_page(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $page = Page::where('slug', 'contact')->sole();

        Livewire::test(ListPages::class)->assertCanSeeTableRecords(Page::all());

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->fillForm(['title' => 'تماس با ایلنیا', 'body' => '- **تلفن:** ۰۲۱۰۰۰۰۰۰۰۰', 'is_published' => true])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue($page->fresh()->is_published);
        $this->get('/contact')->assertOk()->assertSee('تماس با ایلنیا');
    }

    public function test_pages_cannot_be_created_from_panel(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/pages/create')
            ->assertNotFound();
    }
}
