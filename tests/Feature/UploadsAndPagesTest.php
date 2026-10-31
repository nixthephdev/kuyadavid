<?php

namespace Tests\Feature;

use App\Livewire\Admin\Expenses;
use App\Livewire\Admin\Posts;
use App\Models\Expense;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use App\Support\ImageStore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class UploadsAndPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_photos_become_small_webp_files_under_200kb(): void
    {
        $base = app(ImageStore::class)->store(UploadedFile::fake()->image('big.jpg', 4000, 3000), 'updates');

        $this->assertMatchesRegularExpression('#^updates/[a-z0-9]+-1600x1200$#', $base);
        foreach (['lg', 'sm'] as $size) {
            $file = "{$base}-{$size}.webp";
            Storage::disk('public')->assertExists($file);
            $this->assertLessThan(200 * 1024, Storage::disk('public')->size($file));
            $this->assertSame('image/webp', Storage::disk('public')->mimeType($file));
        }
    }

    public function test_admin_can_publish_an_update_with_photos(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(Posts::class)
            ->call('create')
            ->set('title', 'A good week')
            ->set('body', "First paragraph.\n\nSecond paragraph.")
            ->set('picked', [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.png')])
            ->call('save')
            ->assertHasNoErrors();

        $post = Post::sole();
        $this->assertCount(2, $post->photos);
        $this->get('/updates')->assertSee('A good week');
        $this->get('/updates/'.$post->slug)->assertOk()->assertSee('Second paragraph.')->assertSee('.webp');
    }

    public function test_uploads_reject_wrong_types_and_big_files(): void
    {
        $component = Livewire::actingAs(User::factory()->create())->test(Posts::class)->call('create');

        $component->set('picked', [UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf')])
            ->assertHasErrors('picked.0');
        $component->set('picked', [UploadedFile::fake()->image('anim.gif')])
            ->assertHasErrors('picked.0');
        $component->set('picked', [UploadedFile::fake()->image('huge.jpg')->size(6000)])
            ->assertHasErrors('picked.0');
    }

    public function test_transparency_entries_and_totals(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(Expenses::class)
            ->call('create')
            ->set('item', 'Medicine for October')
            ->set('amount', '12,500')
            ->set('spent_on', '2026-10-15')
            ->set('upload', UploadedFile::fake()->image('receipt.jpg'))
            ->call('save')
            ->assertHasNoErrors();

        Expense::create(['spent_on' => '2026-10-20', 'item' => 'Lab tests', 'amount' => 2500.5]);

        $this->assertNotNull(Expense::where('item', 'Medicine for October')->value('receipt'));
        $this->get('/transparency')
            ->assertOk()
            ->assertSee('Medicine for October')
            ->assertSee('₱15,000.50')
            ->assertSee(__('site.transparency.view_receipt'));
    }

    public function test_medical_proof_only_shows_when_uploaded(): void
    {
        $this->get('/story')->assertDontSee(__('site.story.proof_note'));

        Setting::put('medical_proof', [app(ImageStore::class)->store(UploadedFile::fake()->image('cert.jpg'), 'proof')]);

        $this->get('/story')->assertSee(__('site.story.proof_title'))->assertSee('Personal details hidden for privacy.');
    }

    public function test_seo_tags_sitemap_and_robots(): void
    {
        $post = Post::create(['title' => 'Hello world', 'body' => 'Body', 'published_at' => now()->subDay()]);
        Post::create(['title' => 'Future', 'body' => 'Not yet', 'published_at' => now()->addWeek()]);

        $this->get('/')
            ->assertSee('<meta property="og:image"', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('"@type":"WebPage"', false)
            ->assertSee('Website made with love and offered for free by a volunteer. Donations go directly to David&#039;s family.', false);

        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml')
            ->assertSee(route('updates.show', $post))->assertDontSee('future');
        $this->get('/updates/future')->assertNotFound();
        $this->get('/robots.txt')->assertSee('Disallow: /admin')->assertSee('Sitemap:');
    }

    public function test_language_toggle(): void
    {
        $this->get('/lang/fil')->assertRedirect();
        $this->get('/story')->assertSee('lang="fil"', false)->assertSee('Ang Kanyang Kuwento');
        $this->get('/lang/en');
        $this->get('/story')->assertSee('lang="en"', false)->assertSee('His Story');
        $this->get('/lang/de')->assertNotFound();
    }
}
