<?php

namespace Tests\Feature;

use App\Livewire\Admin\DonationDetails;
use App\Livewire\Admin\ShopItems;
use App\Models\Setting;
use App\Models\ShopItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class HelpPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_only_filled_ways_to_give_are_shown(): void
    {
        $this->get('/help')->assertOk()->assertSee(__('site.help.not_set'))->assertDontSee('id="gcash"', false);

        Setting::put(['gcash_name' => 'Tissa Marie Malmis', 'gcash_number' => '0976 139 2052']);

        $this->get('/help')
            ->assertSee('id="gcash"', false)
            ->assertSee('0976 139 2052')
            ->assertDontSee('id="bank"', false)
            ->assertDontSee('id="abroad"', false)
            ->assertDontSee(__('site.help.not_set'));
    }

    public function test_two_banks_and_gofundme(): void
    {
        Setting::put([
            'bank_name' => 'UnionBank', 'bank_account_name' => 'Account One', 'bank_account_number' => '1096 7393 1298',
            'bank2_name' => 'MariBank', 'bank2_account_name' => 'Account Two', 'bank2_qr' => 'donations/qr-500x500',
            'gofundme_url' => 'https://www.gofundme.com/f/example', 'gofundme_organizer' => 'A Friend',
        ]);

        $this->get('/help')
            ->assertSee('UnionBank')->assertSee('1096 7393 1298')
            ->assertSee('MariBank')->assertSee(__('site.help.qr_only'))
            ->assertSee('https://www.gofundme.com/f/example')
            ->assertSee(__('site.help.gofundme_by', ['name' => 'A Friend']));
    }

    public function test_admin_saves_donation_details_and_qr_codes(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(DonationDetails::class)
            ->set('form.bank2_name', 'MariBank')
            ->set('form.gofundme_url', 'https://www.gofundme.com/f/example')
            ->set('qrUploads.bank2_qr', UploadedFile::fake()->image('qr.png', 600, 600))
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('MariBank', Setting::get('bank2_name'));
        $this->assertNotNull($qr = Setting::get('bank2_qr'));
        Storage::disk('public')->assertExists("{$qr}-lg.webp");

        Livewire::actingAs(User::factory()->create())->test(DonationDetails::class)->call('removeQr', 'bank2_qr');
        $this->assertNull(Setting::get('bank2_qr'));
    }

    public function test_donation_links_must_be_https_and_numbers_numeric(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(DonationDetails::class)
            ->set('form.gofundme_url', 'javascript:alert(1)')
            ->set('form.facebook_url', 'http://facebook.com/x')
            ->set('form.bank_account_number', 'call me')
            ->call('save')
            ->assertHasErrors(['form.gofundme_url', 'form.facebook_url', 'form.bank_account_number']);
    }

    public function test_shop_items_show_on_the_help_page(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(ShopItems::class)
            ->call('create')
            ->set('title', 'Sweet Treats for a Cause')
            ->set('description', 'Almond brownies ₱435')
            ->set('upload', UploadedFile::fake()->image('poster.jpg', 1000, 1500))
            ->call('save')
            ->assertHasNoErrors();

        $this->assertNotNull(ShopItem::sole()->photo);
        $this->get('/help')->assertSee(__('site.help.shop_title'))->assertSee('Sweet Treats for a Cause')->assertSee('Almond brownies ₱435');
    }

    public function test_share_message_has_a_copy_button(): void
    {
        Setting::put(['share_message' => 'Please meet David.', 'share_message_fil' => 'Kilalanin po si David.']);

        $this->get('/help')->assertSee('Please meet David.')->assertSee(__('site.help.copy_message'));
        $this->get('/lang/fil');
        $this->get('/help')->assertSee('Kilalanin po si David.');
    }

    public function test_story_shows_videos_when_imported(): void
    {
        $this->get('/story')->assertDontSee('<video', false);

        Setting::put('story_videos', [['src' => 'videos/clip.mp4', 'poster' => null, 'caption' => 'The last video before.']]);

        $this->get('/story')->assertSee('<video', false)->assertSee('preload="none"', false)->assertSee('The last video before.');
    }
}
