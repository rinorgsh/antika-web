<?php

namespace Tests\Feature;

use App\Models\MenuPublication;
use App\Models\MenuSetting;
use App\Services\MenuPublisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarteTest extends TestCase
{
    use RefreshDatabase;

    public function test_initial_data_is_served_before_any_publication(): void
    {
        $this->get('/carte/food-data.js')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/javascript; charset=utf-8')
            ->assertSee('window.FOOD', false);
    }

    public function test_publication_is_live_immediately(): void
    {
        MenuSetting::put('event.enabled', true);
        app(MenuPublisher::class)->publish('test');

        $this->assertSame(1, MenuPublication::count());
        $this->get('/carte/config.json')->assertOk()->assertJsonPath('event.enabled', true);
    }

    public function test_old_qr_code_address_redirects_to_the_menu(): void
    {
        $this->get('http://menu.antika-resto.ovh/menu.pdf/carte.html?lang=nl')
            ->assertRedirect('https://antikaresto.com/carte/carte.html?lang=nl');

        $this->get('http://menu.antika-resto.ovh/menu.pdf')
            ->assertRedirect('https://antikaresto.com/carte/');
    }

    public function test_unknown_files_are_not_served(): void
    {
        $this->get('/carte/.env')->assertNotFound();
        $this->get('/carte/photos/../../.env')->assertNotFound();
    }
}
