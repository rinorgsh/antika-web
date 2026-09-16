<?php

namespace Tests\Feature;

use App\Mail\QuoteAdminNotificationMail;
use App\Mail\QuoteCustomerMail;
use App\Models\BlockedDate;
use App\Models\DrinkCategory;
use App\Models\DrinkOption;
use App\Models\EventMenuFormula;
use App\Models\EventType;
use App\Models\ExtraCategory;
use App\Models\Quote;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SimulatorTest extends TestCase
{
    use RefreshDatabase;

    private EventType $type;
    private Venue $venue;
    private EventMenuFormula $formula;
    private int $dishId;
    private int $softId;
    private int $cavaId;
    private int $extraId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->type = EventType::create(['name' => ['fr' => 'Mariage', 'nl' => 'Huwelijk'], 'slug' => 'mariage']);
        $this->venue = Venue::create(['name' => ['fr' => 'Salle'], 'slug' => 'salle', 'price' => 1000, 'capacity_seated' => 100]);
        $this->formula = EventMenuFormula::create(['name' => ['fr' => 'Classic'], 'slug' => 'classic', 'price_per_person' => 55, 'type' => 'seated']);
        $category = $this->formula->menuCategories()->create(['name' => ['fr' => 'Plat']]);
        $this->dishId = $category->menuItems()->create(['name' => ['fr' => 'Agneau'], 'supplement_price' => 8])->id;

        $drinks = DrinkCategory::create(['name' => ['fr' => 'Softs'], 'slug' => 'softs']);
        $this->softId = $drinks->drinkOptions()->create(['name' => ['fr' => 'Softs'], 'price_all_in' => 6, 'unit_type' => 'glass'])->id;
        $bubbles = DrinkCategory::create(['name' => ['fr' => 'Bulles'], 'slug' => 'bulles', 'role' => 'bubbles']);
        $this->cavaId = $bubbles->drinkOptions()->create(['name' => ['fr' => 'Cava'], 'price_per_unit' => 28, 'unit_type' => 'bottle'])->id;

        $extras = ExtraCategory::create(['name' => ['fr' => 'Déco'], 'slug' => 'deco']);
        $this->extraId = $extras->extraItems()->create(['name' => ['fr' => 'Candy bar'], 'price' => 8, 'price_type' => 'per_person'])->id;
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'first_name' => 'Jean',
            'last_name' => 'Test',
            'email' => 'Jean@Example.com',
            'phone' => '0470000000',
            'event_type_id' => $this->type->id,
            'event_date' => now()->addMonth()->toDateString(),
            'event_time_slot' => 'evening',
            'guest_count' => 50,
            'venue_ids' => [$this->venue->id],
            'menu_formula_id' => $this->formula->id,
            'menu_choices' => [$this->dishId],
            'drinks' => [['id' => $this->softId, 'quantity' => 1], ['id' => $this->cavaId, 'quantity' => 3]],
            'extras' => [['id' => $this->extraId, 'quantity' => 1]],
            'tracking' => ['gclid' => 'abc'],
        ], $override);
    }

    public function test_page_is_rendered_in_the_visitor_language(): void
    {
        $this->get('/events/simulator?lang=nl')
            ->assertOk()
            ->assertSee('Huwelijk')
            ->assertSee('Stel uw feest samen');
    }

    public function test_quote_is_created_with_server_side_prices_and_emails(): void
    {
        Mail::fake();

        $response = $this->withSession(['locale' => 'fr'])->post('/events/simulator', $this->payload());

        $quote = Quote::with('quoteDrinks', 'quoteMenus', 'quoteExtras')->firstOrFail();
        $response->assertRedirect(route('simulator.confirmation', $quote->quote_number));

        // 1000 (salle) + 50×(55+8) (menu) + 50×6 (softs) + 3×28 (cava) + 50×8 (extra)
        $this->assertEquals(1000 + 3150 + 300 + 84 + 400, (float) $quote->subtotal);
        $this->assertSame('fr', $quote->locale);
        $this->assertSame(['gclid' => 'abc'], $quote->tracking);
        $this->assertSame('jean@example.com', $quote->customer->email);

        Mail::assertSent(QuoteCustomerMail::class);
        Mail::assertSent(QuoteAdminNotificationMail::class);

        $this->get(route('simulator.confirmation', $quote->quote_number))->assertOk();
    }

    public function test_confirmation_of_another_quote_is_not_accessible(): void
    {
        Mail::fake();
        $this->post('/events/simulator', $this->payload());
        $number = Quote::value('quote_number');

        $this->flushSession();
        $this->get(route('simulator.confirmation', $number))->assertNotFound();
        $this->get(route('simulator.pdf', $number))->assertNotFound();
    }

    public function test_blocked_date_is_refused(): void
    {
        $date = now()->addMonth()->toDateString();
        BlockedDate::create(['date' => $date, 'venue_id' => null]);

        $this->postJson('/events/simulator/availability', ['date' => $date])
            ->assertOk()->assertJson([$this->venue->id => false]);

        $this->post('/events/simulator', $this->payload(['event_date' => $date]))
            ->assertSessionHasErrors('venue_ids');
        $this->assertSame(0, Quote::count());
    }

    public function test_dish_from_another_formula_is_ignored(): void
    {
        Mail::fake();
        $other = EventMenuFormula::create(['name' => ['fr' => 'Autre'], 'slug' => 'autre', 'price_per_person' => 1]);
        $foreignDish = $other->menuCategories()->create(['name' => ['fr' => 'X']])
            ->menuItems()->create(['name' => ['fr' => 'Homard'], 'supplement_price' => 100])->id;

        $this->post('/events/simulator', $this->payload(['menu_choices' => [$foreignDish], 'drinks' => [], 'extras' => []]));

        $this->assertEquals(0, (float) Quote::first()->quoteMenus->first()->supplements_total);
    }

    public function test_honeypot_blocks_bots(): void
    {
        $this->post('/events/simulator', $this->payload(['website' => 'http://spam']))->assertRedirect();
        $this->assertSame(0, Quote::count());
    }

    public function test_landing_page_preselects_event_type(): void
    {
        $this->get('/events/wedding?lang=fr')->assertOk()->assertSee('&quot;eventType&quot;:&quot;mariage&quot;', false);
    }
}
