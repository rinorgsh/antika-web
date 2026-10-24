<?php

namespace Tests\Feature;

use App\Mail\SponsorApplicationAdminMail;
use App\Mail\SponsorApplicationCustomerMail;
use App\Models\SponsorApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class SponsorsTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $override = []): array
    {
        return array_merge([
            'participation_type' => 'sponsor_exhibitor',
            'name' => 'Arta Krasniqi',
            'organization' => 'Krasniqi Bakery',
            'phone' => '0470 12 34 56',
            'email' => 'Arta@Example.com',
            'activity' => 'Boulangerie',
            'promote' => 'Pâtisseries',
            'sell' => 'Byrek',
            'message' => '',
            'locale' => 'sq',
        ], $override);
    }

    public function test_page_is_french_by_default_and_switches_language(): void
    {
        $this->get('/sponsors')->assertOk()->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Sponsors')->where('lang', 'fr')->where('strings.hero.title', 'Festival 1912'));

        $this->get('/sponsors?lang=sq')->assertOk()->assertInertia(fn (AssertableInertia $page) => $page
            ->where('lang', 'sq')->where('strings.hero.title', 'Festivali 1912'));

        // Choix mémorisé pour la visite suivante.
        $this->get('/sponsors')->assertInertia(fn (AssertableInertia $page) => $page->where('lang', 'sq'));

        $this->get('/sponsors?lang=nl')->assertInertia(fn (AssertableInertia $page) => $page
            ->where('lang', 'nl')->where('strings.form.type_title', 'Soort deelname'));
    }

    public function test_application_is_stored_and_emails_are_sent(): void
    {
        Mail::fake();

        $this->post('/sponsors', $this->payload())->assertRedirect()->assertSessionHas('sponsor_sent');

        $a = SponsorApplication::sole();
        $this->assertSame('sponsor_exhibitor', $a->participation_type);
        $this->assertSame('arta@example.com', $a->email);
        $this->assertSame('sq', $a->locale);
        $this->assertSame('new', $a->status);

        Mail::assertSent(SponsorApplicationAdminMail::class);
        Mail::assertSent(SponsorApplicationCustomerMail::class, fn ($m) => $m->hasTo('arta@example.com') && $m->locale === 'sq');
    }

    public function test_emails_render(): void
    {
        $a = SponsorApplication::create(array_merge($this->payload(), ['email' => 'arta@example.com']));

        $this->assertStringContainsString('Krasniqi Bakery', (new SponsorApplicationAdminMail($a))->render());
        $this->assertStringContainsString('Përshëndetje Arta Krasniqi', (new SponsorApplicationCustomerMail($a))->render());
    }

    public function test_validation_messages_follow_the_page_language(): void
    {
        $this->post('/sponsors', $this->payload(['participation_type' => '', 'phone' => '12', 'locale' => 'nl']))
            ->assertSessionHasErrors([
                'participation_type' => 'Kies een soort deelname.',
                'phone' => 'Vul een geldig telefoonnummer in.',
            ]);

        $this->assertDatabaseCount('sponsor_applications', 0);
    }

    public function test_organization_is_optional_for_donors_only(): void
    {
        Mail::fake();

        $this->post('/sponsors', $this->payload(['organization' => '', 'locale' => 'fr']))
            ->assertSessionHasErrors(['organization' => 'Veuillez indiquer le nom de votre entreprise ou organisation.']);

        $this->post('/sponsors', $this->payload(['participation_type' => 'donor', 'organization' => '']))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseCount('sponsor_applications', 1);
    }

    public function test_honeypot_drops_bots(): void
    {
        $this->post('/sponsors', $this->payload(['website' => 'spam.example']))->assertRedirect();

        $this->assertDatabaseCount('sponsor_applications', 0);
    }

    public function test_admin_pages_load(): void
    {
        $a = SponsorApplication::create(array_merge($this->payload(), ['email' => 'arta@example.com']));
        $this->actingAs(User::factory()->create());

        $this->get('/admin/festival-1912')->assertOk()->assertSee('Arta Krasniqi');
        $this->get("/admin/festival-1912/{$a->id}")->assertOk()->assertSee('Krasniqi Bakery');
        $this->get("/admin/festival-1912/{$a->id}/edit")->assertOk();
    }
}
