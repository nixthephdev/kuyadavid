<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public static function adminPages(): array
    {
        return array_map(fn ($p) => [$p], [
            '/admin', '/admin/fundraising', '/admin/updates', '/admin/transparency', '/admin/prayers',
            '/admin/donations', '/admin/needs', '/admin/shop', '/admin/messages', '/admin/story',
        ]);
    }

    #[DataProvider('adminPages')]
    public function test_guests_are_sent_to_login(string $url): void
    {
        $this->get($url)->assertRedirect('/admin/login');
    }

    #[DataProvider('adminPages')]
    public function test_signed_in_admin_can_open_every_screen(string $url): void
    {
        $this->actingAs(User::factory()->create())->get($url)->assertOk();
    }

    public function test_there_is_no_public_registration(): void
    {
        $this->get('/register')->assertNotFound();
        $this->get('/admin/register')->assertNotFound();
        $this->post('/register', ['email' => 'x@example.com'])->assertNotFound();
    }

    public function test_admin_can_sign_in_and_out(): void
    {
        $user = User::factory()->create(['email' => 'family@example.com', 'password' => 'secret-pass']);

        $this->post('/admin/login', ['email' => 'family@example.com', 'password' => 'secret-pass'])
            ->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);

        $this->post('/admin/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_wrong_password_is_rejected(): void
    {
        User::factory()->create(['email' => 'family@example.com', 'password' => 'secret-pass']);

        $this->from('/admin/login')
            ->post('/admin/login', ['email' => 'family@example.com', 'password' => 'wrong'])
            ->assertRedirect('/admin/login')
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_is_throttled(): void
    {
        foreach (range(1, 5) as $i) {
            $this->post('/admin/login', ['email' => 'a@example.com', 'password' => 'nope']);
        }

        $this->post('/admin/login', ['email' => 'a@example.com', 'password' => 'nope'])->assertStatus(429);
    }

    public function test_forms_require_csrf_tokens(): void
    {
        // Laravel skips CSRF checks in tests, so check the middleware is on the web group instead.
        $this->assertContains(
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            app(\Illuminate\Contracts\Http\Kernel::class)->getMiddlewareGroups()['web'],
        );
        $this->get('/admin/login')->assertSee('name="_token"', false);
    }

    public function test_admin_seeder_uses_env_values(): void
    {
        config(['site.admin' => ['name' => 'Family', 'email' => 'seeded@example.com', 'password' => 'from-env-123']]);

        $this->seed(\Database\Seeders\AdminSeeder::class);

        $this->assertTrue(auth()->attempt(['email' => 'seeded@example.com', 'password' => 'from-env-123']));
    }
}
