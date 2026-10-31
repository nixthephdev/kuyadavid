<?php

namespace Tests\Feature;

use App\Livewire\Admin\Fundraising;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProgressBarTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_shows_no_progress_bar_without_a_goal(): void
    {
        Setting::put('raised_amount', 5000);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('role="progressbar"', false)
            ->assertDontSee(__('site.home.progress_title'));
    }

    public function test_home_shows_amounts_percent_and_date_from_the_admin(): void
    {
        Setting::put([
            'goal_amount' => 500000,
            'raised_amount' => 137500,
            'progress_updated_at' => '2026-10-30T10:00:00+08:00',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('aria-valuenow="27"', false)
            ->assertSee('₱137,500')
            ->assertSee('₱500,000')
            ->assertSee('October 30, 2026');
    }

    public function test_bar_is_capped_at_100_percent(): void
    {
        Setting::put(['goal_amount' => 1000, 'raised_amount' => 1500]);

        $this->get('/')->assertSee('aria-valuenow="100"', false)->assertSee('width: 100%', false);
    }

    public function test_admin_saves_goal_strips_commas_and_stamps_the_date(): void
    {
        $this->travelTo('2026-11-02 09:30:00');

        Livewire::actingAs(User::factory()->create())
            ->test(Fundraising::class)
            ->set('goal', '500,000')
            ->set('raised', '250,000.50')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('500000', Setting::get('goal_amount'));
        $this->assertSame('250000.50', Setting::get('raised_amount'));
        $this->assertStringStartsWith('2026-11-02', Setting::get('progress_updated_at'));
    }

    public function test_clearing_the_goal_hides_the_bar(): void
    {
        Setting::put(['goal_amount' => 1000, 'raised_amount' => 10]);

        Livewire::actingAs(User::factory()->create())
            ->test(Fundraising::class)
            ->set('goal', '')
            ->call('save')
            ->assertHasNoErrors();

        $this->get('/')->assertDontSee('role="progressbar"', false);
    }

    public function test_admin_rejects_words_and_negative_amounts(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(Fundraising::class)
            ->set('goal', 'five hundred')
            ->set('raised', '-10')
            ->call('save')
            ->assertHasErrors(['goal' => 'numeric', 'raised' => 'min']);
    }
}
