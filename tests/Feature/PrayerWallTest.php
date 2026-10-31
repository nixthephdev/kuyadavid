<?php

namespace Tests\Feature;

use App\Livewire\Admin\Prayers;
use App\Livewire\PrayerWall;
use App\Models\Prayer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class PrayerWallTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('prayer:127.0.0.1');
    }

    public function test_new_prayer_is_saved_as_pending_and_hidden(): void
    {
        Livewire::test(PrayerWall::class)
            ->set('name', 'Ana')
            ->set('country', 'Philippines')
            ->set('message', 'Praying for your healing, David.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true);

        $prayer = Prayer::sole();
        $this->assertNull($prayer->approved_at);
        $this->assertNotNull($prayer->ip_hash);
        $this->assertNotSame('127.0.0.1', $prayer->ip_hash);

        $this->get('/prayer-wall')->assertDontSee('Praying for your healing, David.');
        $this->get('/')->assertDontSee('Praying for your healing, David.');
    }

    public function test_admin_approval_makes_it_public(): void
    {
        $prayer = Prayer::create(['country' => 'Canada', 'message' => 'Sending love from Toronto.']);

        Livewire::actingAs(User::factory()->create())
            ->test(Prayers::class)
            ->assertSee('Sending love from Toronto.')
            ->call('approve', $prayer->id)
            ->assertDispatched('notify');

        $this->assertNotNull($prayer->fresh()->approved_at);
        $this->get('/prayer-wall')->assertSee('Sending love from Toronto.')->assertSee(__('site.prayers.anonymous'));
        $this->get('/')->assertSee('Sending love from Toronto.');
    }

    public function test_admin_can_hide_and_delete(): void
    {
        $prayer = Prayer::create(['country' => 'Japan', 'message' => 'Hello there', 'approved_at' => now()]);
        $admin = Livewire::actingAs(User::factory()->create())->test(Prayers::class);

        $admin->call('hide', $prayer->id);
        $this->get('/prayer-wall')->assertDontSee('Hello there');

        $admin->call('delete', $prayer->id);
        $this->assertModelMissing($prayer);
    }

    public function test_country_and_message_are_required(): void
    {
        Livewire::test(PrayerWall::class)
            ->call('submit')
            ->assertHasErrors(['country' => 'required', 'message' => 'required'])
            ->assertHasNoErrors('name');

        $this->assertSame(0, Prayer::count());
    }

    public function test_message_length_is_limited(): void
    {
        Livewire::test(PrayerWall::class)
            ->set('country', 'Philippines')
            ->set('message', str_repeat('a', 601))
            ->call('submit')
            ->assertHasErrors(['message' => 'max']);
    }

    public function test_html_is_shown_as_text_not_run(): void
    {
        Prayer::create(['country' => 'X', 'message' => '<script>alert(1)</script>', 'approved_at' => now()]);

        $this->get('/prayer-wall')
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_honeypot_silently_drops_bots(): void
    {
        Livewire::test(PrayerWall::class)
            ->set('country', 'Botland')
            ->set('message', 'Buy cheap pills')
            ->set('website', 'http://spam.example')
            ->call('submit')
            ->assertSet('sent', true);

        $this->assertSame(0, Prayer::count());
    }

    public function test_rate_limit_stops_flooding(): void
    {
        foreach (range(1, 4) as $i) {
            $component = Livewire::test(PrayerWall::class)
                ->set('country', 'Philippines')
                ->set('message', "Prayer number {$i}")
                ->call('submit');
        }

        $component->assertHasErrors('message');
        $this->assertSame(3, Prayer::count());
    }
}
