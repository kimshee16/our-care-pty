<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\CmsContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class InquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_navigation_links_to_the_inquiry_form(): void
    {
        foreach (['/', '/home-v2', '/about'] as $path) {
            $this->get($path)->assertOk()->assertSee('href="'.route('inquiry.create').'">Inquire</a>', false);
        }

        $this->get('/inquire')->assertOk()
            ->assertSee('action="'.route('inquiry.store').'"', false)
            ->assertSee('name="_token"', false);
    }

    public function test_valid_inquiry_goes_to_the_configured_contact_email(): void
    {
        CmsContent::set('brand.email', 'care-team@example.com');

        $this->followingRedirects()
            ->post('/inquire', [
                'name' => 'Sample Client',
                'email' => 'client@example.com',
                'message' => 'Do you provide transport support?',
            ])
            ->assertOk()
            ->assertSee('We’ve received your inquiry.');

        $messages = Mail::mailer('array')->getSymfonyTransport()->messages();
        $this->assertCount(1, $messages);
        $email = $messages->sole()->getOriginalMessage();
        $this->assertSame('care-team@example.com', $email->getTo()[0]->getAddress());
        $this->assertSame('client@example.com', $email->getReplyTo()[0]->getAddress());
        $this->assertStringContainsString('Do you provide transport support?', $email->getTextBody());
        $this->assertDatabaseHas('inquiries', [
            'email' => 'client@example.com',
            'phone' => null,
        ]);
    }

    public function test_invalid_inquiry_returns_errors_and_preserves_valid_text(): void
    {
        $this->from('/inquire')->post('/inquire', [
            'name' => ['invalid'],
            'email' => 'invalid',
            'message' => 'A question about support.',
        ])->assertRedirect('/inquire')->assertSessionHasErrors(['name', 'email']);

        $this->get('/inquire')->assertOk()->assertSee('A question about support.');
        $this->assertDatabaseEmpty('inquiries');
        $this->assertCount(0, Mail::mailer('array')->getSymfonyTransport()->messages());
    }

    public function test_admin_can_review_an_inquiry_even_when_email_fails(): void
    {
        Mail::shouldReceive('raw')->once()->andThrow(new TransportException('Mail unavailable'));

        $this->post('/inquire', [
            'name' => 'Sample Client',
            'email' => 'client@example.com',
            'phone' => '0400 123 456',
            'message' => 'Do you provide <strong>support</strong>?',
        ])->assertRedirect('/inquire')->assertSessionHas('inquiry_status');

        $admin = User::factory()->create(['accounttype' => 'admin']);
        $this->withSession(['user' => ['id' => $admin->id, 'accounttype' => 'admin']])
            ->get('/admin/inquiries')
            ->assertOk()
            ->assertSee('nav-item active')
            ->assertSee('Sample Client')
            ->assertSee('client@example.com')
            ->assertSee('0400 123 456')
            ->assertSee('Do you provide <strong>support</strong>?')
            ->assertDontSee('<strong>support</strong>', false);
    }

    public function test_non_admins_cannot_view_inquiries(): void
    {
        $this->get('/admin/inquiries')->assertRedirect('/login');

        $this->withSession(['user' => ['accounttype' => 'client']])
            ->get('/admin/inquiries')->assertRedirect('/client-dashboard');

        $this->withSession(['user' => ['accounttype' => 'healthcare_worker']])
            ->get('/admin/inquiries')->assertRedirect('/healthcare-jobs');

        $this->withSession(['user' => ['accounttype' => 'unknown']])
            ->get('/admin/inquiries')->assertForbidden();
    }
}
