<?php

namespace Tests\Feature;

use App\Livewire\AddressesPage;
use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Tests\TestCase;

class AddressesPageTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create(['mobile' => '09121234567', 'name' => 'مشتری نمونه']);
        $this->actingAs($this->user);
    }

    private function validForm(): array
    {
        return [
            'recipient_name' => 'علی نمونه',
            'mobile' => '۰۹۳۵ ۱۲۳ ۴۵۶۷',
            'province' => 'فارس',
            'city' => 'شیراز',
            'address_line' => 'خیابان نمونه، کوچهٔ ۳، پلاک ۱۲',
            'postal_code' => '۷۱۳۴۵-۶۷۸۹۰',
        ];
    }

    public function test_guest_is_redirected_to_login(): void
    {
        auth()->logout();

        $this->get('/account/addresses')->assertRedirect(route('login'));
    }

    public function test_page_loads_with_empty_state_and_header_link(): void
    {
        $this->get('/account/addresses')
            ->assertOk()
            ->assertSee('<title>آدرس‌های من | ایلنیا</title>', false)
            ->assertSee('هنوز آدرسی ثبت نکرده‌اید.')
            ->assertSee('حساب من');
    }

    public function test_new_form_is_prefilled_with_user_name_and_mobile(): void
    {
        Livewire::test(AddressesPage::class)
            ->call('create')
            ->assertSet('showForm', true)
            ->assertSet('recipient_name', 'مشتری نمونه')
            ->assertSet('mobile', '09121234567');
    }

    public function test_add_address_normalizes_persian_input(): void
    {
        Livewire::test(AddressesPage::class)
            ->call('create')
            ->set($this->validForm())
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('showForm', false)
            ->assertSee('آدرس ذخیره شد.');

        $address = $this->user->addresses()->sole();
        $this->assertSame('09351234567', $address->mobile);
        $this->assertSame('7134567890', $address->postal_code);
        $this->assertSame('فارس', $address->province);
    }

    public function test_validation_errors_are_persian(): void
    {
        Livewire::test(AddressesPage::class)
            ->call('create')
            ->set(['recipient_name' => '', 'mobile' => '021', 'province' => 'آتلانتیس', 'city' => '', 'address_line' => 'کوتاه', 'postal_code' => '123'])
            ->call('save')
            ->assertHasErrors(['recipient_name', 'mobile', 'province', 'city', 'address_line', 'postal_code'])
            ->assertSee('نام گیرنده را وارد کنید.')
            ->assertSee('شمارهٔ موبایل معتبر نیست')
            ->assertSee('استان انتخاب‌شده معتبر نیست.')
            ->assertSee('کد پستی باید 10 رقم باشد.');

        $this->assertSame(0, Address::count());
    }

    public function test_edit_address(): void
    {
        $address = Address::factory()->for($this->user)->create(['city' => 'تهران']);

        Livewire::test(AddressesPage::class)
            ->call('edit', $address->id)
            ->assertSet('editingId', $address->id)
            ->assertSet('city', 'تهران')
            ->set('city', 'کرج')
            ->set('province', 'البرز')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('آدرس به‌روز شد.');

        $this->assertSame('کرج', $address->fresh()->city);
        $this->assertSame(1, Address::count());
    }

    public function test_delete_address(): void
    {
        $address = Address::factory()->for($this->user)->create();

        Livewire::test(AddressesPage::class)->call('delete', $address->id)->assertSee('آدرس حذف شد.');

        $this->assertModelMissing($address);
    }

    public function test_cannot_see_others_addresses(): void
    {
        Address::factory()->create(['address_line' => 'نشانی مشتری دیگر']);

        $this->get('/account/addresses')->assertDontSee('نشانی مشتری دیگر');
    }

    public function test_cannot_edit_others_address(): void
    {
        $others = Address::factory()->create();

        Livewire::test(AddressesPage::class)->call('edit', $others->id)->assertNotFound();
    }

    public function test_cannot_delete_others_address(): void
    {
        $others = Address::factory()->create();

        Livewire::test(AddressesPage::class)->call('delete', $others->id)->assertNotFound();

        $this->assertModelExists($others);
    }

    public function test_editing_id_cannot_be_set_from_browser(): void
    {
        $others = Address::factory()->create();

        $this->expectException(CannotUpdateLockedPropertyException::class);
        Livewire::test(AddressesPage::class)->set('editingId', $others->id);
    }

    public function test_maximum_number_of_addresses(): void
    {
        Address::factory()->count(AddressesPage::MAX_ADDRESSES)->for($this->user)->create();

        Livewire::test(AddressesPage::class)
            ->assertDontSee('افزودن آدرس')
            ->call('create')
            ->set($this->validForm())
            ->call('save')
            ->assertSee('حداکثر ۱۰ آدرس');

        $this->assertSame(AddressesPage::MAX_ADDRESSES, $this->user->addresses()->count());
    }

    public function test_deleting_user_deletes_addresses(): void
    {
        Address::factory()->for($this->user)->create();

        $this->user->delete();

        $this->assertSame(0, Address::count());
    }
}
