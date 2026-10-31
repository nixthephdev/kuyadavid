<?php

namespace Tests\Feature;

use App\Livewire\ContactForm;
use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('contact:127.0.0.1');
        config(['site.family_email' => 'family@example.com']);
        Mail::fake();
    }

    private function filled(): \Livewire\Features\SupportTesting\Testable
    {
        return Livewire::test(ContactForm::class)
            ->set('name', 'Dr. Reyes')
            ->set('email', 'reyes@example.com')
            ->set('topic', 'medical')
            ->set('message', 'I work with a rare-disease foundation and would like to help.');
    }

    public function test_required_fields(): void
    {
        Livewire::test(ContactForm::class)
            ->set('topic', '')
            ->call('submit')
            ->assertHasErrors(['name' => 'required', 'email' => 'required', 'message' => 'required', 'topic' => 'required'])
            ->assertHasNoErrors('phone');

        $this->assertSame(0, ContactMessage::count());
        Mail::assertNothingSent();
    }

    public function test_invalid_values_are_rejected(): void
    {
        $this->filled()
            ->set('email', 'not-an-email')
            ->set('phone', 'call me maybe')
            ->set('topic', 'hacking')
            ->set('message', 'short')
            ->call('submit')
            ->assertHasErrors(['email' => 'email', 'phone' => 'regex', 'topic' => 'in', 'message' => 'min']);
    }

    public function test_valid_message_is_saved_and_emailed_to_the_family(): void
    {
        $this->filled()
            ->set('phone', '+63 917 000 0000')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true);

        $message = ContactMessage::sole();
        $this->assertSame('medical', $message->topic);
        $this->assertNull($message->read_at);

        Mail::assertSent(NewContactMessage::class, function (NewContactMessage $mail) {
            return $mail->hasTo('family@example.com') && $mail->hasReplyTo('reyes@example.com');
        });
    }

    public function test_message_is_kept_even_if_email_fails(): void
    {
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP down'));

        $this->filled()->call('submit')->assertSet('sent', true);

        $this->assertSame(1, ContactMessage::count());
    }

    public function test_topic_comes_from_the_link(): void
    {
        $this->get('/contact?topic=supplies')->assertOk()->assertSeeHtml('value="supplies"');
        Livewire::test(ContactForm::class, ['topic' => 'nonsense'])->assertSet('topic', 'general');
        $this->get('/contact?topic[]=x')->assertOk();
    }

    public function test_honeypot_and_rate_limit(): void
    {
        $this->filled()->set('website', 'spam')->call('submit')->assertSet('sent', true);
        $this->assertSame(0, ContactMessage::count());

        foreach (range(1, 4) as $i) {
            $component = $this->filled()->call('submit');
        }
        $component->assertHasErrors('message');
        $this->assertSame(3, ContactMessage::count());
    }
}
