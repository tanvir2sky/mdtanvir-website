<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Notifications\NewContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.turnstile.site_key' => null,
            'services.turnstile.secret_key' => null,
            'app.admin_email' => 'admin@example.com',
        ]);
    }

    public function test_successful_submit_redirects_to_contact_section_with_status(): void
    {
        Notification::fake();

        $response = $this->post(route('contact.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'subject' => 'Hello',
            'message' => 'I have a project for you.',
        ]);

        $response->assertRedirect(route('home').'#contact');
        $response->assertSessionHas('contact_status');
        $this->assertDatabaseHas('contact_messages', ['email' => 'jane@example.com']);

        Notification::assertSentOnDemand(NewContactMessage::class);

        $this->followRedirects($response)->assertSee('your message has been sent successfully', false);
    }

    public function test_invalid_submit_redirects_to_contact_section_with_errors_and_input(): void
    {
        $response = $this->from(route('home'))->post(route('contact.store'), [
            'name' => 'Jane Doe',
            'email' => 'not-an-email',
            'subject' => 'Hello',
            'message' => 'Hi',
        ]);

        $response->assertRedirect(route('home').'#contact');
        $response->assertSessionHasErrors('email');
        $response->assertSessionHasInput('name', 'Jane Doe');
        $this->assertSame(0, ContactMessage::count());
    }

    public function test_contact_endpoint_is_rate_limited(): void
    {
        Notification::fake();

        $payload = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'subject' => 'Hello',
            'message' => 'Hello again',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('contact.store'), $payload)->assertRedirect();
        }

        $this->post(route('contact.store'), $payload)->assertStatus(429);
    }
}
