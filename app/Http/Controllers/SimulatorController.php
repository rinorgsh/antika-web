<?php

namespace App\Http\Controllers;

use App\Mail\QuoteAdminNotificationMail;
use App\Mail\QuoteCustomerMail;
use App\Models\Customer;
use App\Models\DrinkCategory;
use App\Models\DrinkOption;
use App\Models\EventMenuFormula;
use App\Models\EventMenuItem;
use App\Models\EventSetting;
use App\Models\EventType;
use App\Models\ExtraCategory;
use App\Models\ExtraItem;
use App\Models\Quote;
use App\Models\Venue;
use App\Services\QuotePdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * Simulateur de devis « événements & location de salle » (/events/simulator).
 *
 * Repris de l'ancienne application baba-event. Les textes du catalogue sont
 * envoyés à la page déjà traduits dans la langue du visiteur.
 */
class SimulatorController extends Controller
{
    public function index(Request $request): Response
    {
        $l = app()->getLocale();

        return Inertia::render('Simulator/Index', [
            'strings' => trans('simulator'),
            // Type présélectionné par une page d'atterrissage (?type=mariage).
            'preselectedType' => $request->query('type'),

            'eventTypes' => EventType::active()->ordered()->get()->map(fn (EventType $t) => [
                'id' => $t->id,
                'slug' => $t->slug,
                'icon' => $t->icon,
                'name' => $t->tr('name', $l),
                'description' => $t->tr('description', $l),
            ]),

            'venues' => Venue::active()->ordered()->with('venueImages')->get()->map(fn (Venue $v) => [
                'id' => $v->id,
                'name' => $v->tr('name', $l),
                'description' => $v->tr('short_description', $l) ?: $v->tr('description', $l),
                'image' => $v->coverUrl(),
                'capacity' => $v->capacity_seated,
                'has_parking' => $v->has_parking,
                'has_vestiaire' => $v->has_vestiaire,
                'has_private_toilets' => $v->has_private_toilets,
            ]),

            'menuFormulas' => EventMenuFormula::active()->ordered()
                ->with(['menuCategories.menuItems' => fn ($q) => $q->where('is_active', true)])
                ->get()->map(fn (EventMenuFormula $f) => [
                    'id' => $f->id,
                    'name' => $f->tr('name', $l),
                    'description' => $f->tr('short_description', $l) ?: $f->tr('description', $l),
                    'type' => $f->type,
                    'image' => EventMenuFormula::imageUrl($f->image_path),
                    'categories' => $f->menuCategories->map(fn ($c) => [
                        'id' => $c->id,
                        'name' => $c->tr('name', $l),
                        'items' => $c->menuItems->map(fn (EventMenuItem $i) => [
                            'id' => $i->id,
                            'name' => $i->tr('name', $l),
                            'description' => $i->tr('description', $l),
                            'image' => EventMenuItem::imageUrl($i->image_path),
                            'has_supplement' => (float) $i->supplement_price > 0,
                        ])->values(),
                    ])->filter(fn ($c) => $c['items']->isNotEmpty())->values(),
                ]),

            'drinkCategories' => DrinkCategory::ordered()
                ->with(['drinkOptions' => fn ($q) => $q->where('is_active', true)])
                ->get()
                ->filter(fn ($c) => $c->drinkOptions->isNotEmpty())
                ->map(fn (DrinkCategory $c) => [
                    'id' => $c->id,
                    'name' => $c->tr('name', $l),
                    'role' => $c->role,
                    'options' => $c->drinkOptions->map(fn (DrinkOption $o) => [
                        'id' => $o->id,
                        'name' => $o->tr('name', $l),
                        'description' => $o->tr('description', $l),
                        'image' => DrinkOption::imageUrl($o->image_path),
                        'unit_type' => $o->unit_type,
                        'all_in' => $o->price_all_in !== null,
                    ])->values(),
                ])->values(),

            'extraCategories' => ExtraCategory::ordered()
                ->with(['extraItems' => fn ($q) => $q->where('is_active', true)])
                ->get()
                ->filter(fn ($c) => $c->extraItems->isNotEmpty())
                ->map(fn (ExtraCategory $c) => [
                    'id' => $c->id,
                    'name' => $c->tr('name', $l),
                    'items' => $c->extraItems->map(fn (ExtraItem $i) => [
                        'id' => $i->id,
                        'name' => $i->tr('name', $l),
                        'description' => $i->tr('description', $l),
                        'image' => ExtraItem::imageUrl($i->image_path),
                        'price_type' => $i->price_type,
                        'exclusive_group' => $i->exclusive_group,
                        'is_default' => $i->is_default,
                    ])->values(),
                ])->values(),

            'legal' => [
                'terms' => EventSetting::get('terms_url'),
                'privacy' => EventSetting::get('privacy_url'),
            ],
        ]);
    }

    /** Disponibilité des salles pour une date : {venue_id: bool}. */
    public function availability(Request $request): JsonResponse
    {
        $data = $request->validate(['date' => 'required|date|after:today']);

        return response()->json(
            Venue::active()->get()->mapWithKeys(fn (Venue $v) => [$v->id => $v->isAvailableOn($data['date'])])
        );
    }

    public function submit(Request $request): RedirectResponse
    {
        // Pot de miel anti-robots : champ invisible pour un humain.
        if (filled($request->input('website'))) {
            return redirect()->route('simulator.index');
        }

        $v = $request->validate([
            'first_name' => 'required|string|max:120',
            'last_name' => 'required|string|max:120',
            'email' => 'required|email|max:190',
            'phone' => 'required|string|max:50',
            'company' => 'nullable|string|max:190',
            'event_type_id' => 'required|exists:event_types,id',
            'event_date' => 'required|date|after:today',
            'event_time_slot' => 'required|in:day,evening,full_day',
            'guest_count' => 'required|integer|min:1|max:2000',
            'child_menu' => 'boolean',
            'dietary_requirements' => 'nullable|string|max:1000',
            'special_requests' => 'nullable|string|max:2000',
            'venue_ids' => 'required|array|min:1',
            'venue_ids.*' => 'integer|exists:venues,id',
            'menu_formula_id' => 'required|exists:event_menu_formulas,id',
            'menu_choices' => 'nullable|array',
            'menu_choices.*' => 'integer|exists:event_menu_items,id',
            'drinks' => 'nullable|array',
            'drinks.*.id' => 'required|integer|exists:drink_options,id',
            'drinks.*.quantity' => 'required|integer|min:1|max:5000',
            'extras' => 'nullable|array',
            'extras.*.id' => 'required|integer|exists:extra_items,id',
            'extras.*.quantity' => 'required|integer|min:1|max:5000',
            'tracking' => 'nullable|array',
            'tracking.*' => 'nullable|string|max:255',
        ]);

        // Contrôle d'une date prise entre-temps (l'écran le signale déjà, on
        // revérifie ici : deux demandes peuvent viser la même date).
        $unavailable = Venue::whereIn('id', $v['venue_ids'])->get()
            ->reject(fn (Venue $venue) => $venue->isAvailableOn($v['event_date']));
        if ($unavailable->isNotEmpty()) {
            throw ValidationException::withMessages([
                'venue_ids' => trans('simulator.errors.venue_unavailable', [
                    'venues' => $unavailable->map->tr('name')->join(', '),
                ]),
            ]);
        }

        $quote = DB::transaction(fn () => $this->createQuote($v));
        $quote->load(Quote::FULL);

        try {
            Mail::to($quote->customer->email)->send(new QuoteCustomerMail($quote));
        } catch (\Throwable $e) {
            Log::error('Envoi du mail client (devis) impossible', ['quote' => $quote->quote_number, 'error' => $e->getMessage()]);
        }

        try {
            Mail::to(EventSetting::notifyRecipients())->send(new QuoteAdminNotificationMail($quote));
        } catch (\Throwable $e) {
            Log::error('Envoi de la notification admin (devis) impossible', ['quote' => $quote->quote_number, 'error' => $e->getMessage()]);
        }

        // La page de confirmation n'est accessible qu'une fois, à ce navigateur :
        // un numéro de devis deviné ne donne pas accès aux coordonnées d'un autre client.
        session()->put('simulator.quote', $quote->quote_number);
        session()->flash('simulator.just_submitted', true);

        return redirect()->route('simulator.confirmation', $quote->quote_number);
    }

    public function confirmation(string $quoteNumber): Response
    {
        abort_unless(session('simulator.quote') === $quoteNumber, 404);
        $quote = Quote::where('quote_number', $quoteNumber)->with('customer')->firstOrFail();

        return Inertia::render('Simulator/Confirmation', [
            'strings' => trans('simulator'),
            'quote' => [
                'number' => $quote->quote_number,
                'first_name' => $quote->customer->first_name,
                'guests' => $quote->guestCount(),
                // Estimation (hors TVA) transmise à Google Ads comme valeur de conversion.
                'value' => (float) $quote->subtotal,
            ],
            // Conversion à déclarer une seule fois (pas en cas de rechargement).
            'trackConversion' => (bool) session('simulator.just_submitted'),
            'whatsapp' => EventSetting::get('whatsapp'),
        ]);
    }

    public function pdf(string $quoteNumber): HttpResponse
    {
        abort_unless(session('simulator.quote') === $quoteNumber, 404);
        $quote = Quote::where('quote_number', $quoteNumber)->with(Quote::FULL)->firstOrFail();

        return QuotePdf::make($quote, showPrices: false)
            ->download(trans('simulator.pdf.filename', ['number' => $quote->quote_number], $quote->locale).'.pdf');
    }

    /* ------------------------------------------------------------------ */

    /**
     * Crée le devis et ses lignes. Tous les prix sont relus en base : le
     * navigateur n'envoie que des identifiants et des quantités.
     */
    private function createQuote(array $v): Quote
    {
        $customer = Customer::firstOrNew(['email' => mb_strtolower(trim($v['email']))]);
        $customer->fill([
            'first_name' => $v['first_name'],
            'last_name' => $v['last_name'],
            'phone' => $v['phone'],
            'company' => $v['company'] ?? $customer->company,
        ])->save();

        $guests = (int) $v['guest_count'];

        $quote = Quote::create([
            'quote_number' => Quote::generateQuoteNumber(),
            'customer_id' => $customer->id,
            'event_type_id' => $v['event_type_id'],
            'event_date' => $v['event_date'],
            'event_time_slot' => $v['event_time_slot'],
            'guest_count_adults' => $guests,
            'guest_count_children' => 0,
            'child_menu' => (bool) ($v['child_menu'] ?? false),
            'dietary_requirements' => $v['dietary_requirements'] ?? null,
            'special_requests' => $v['special_requests'] ?? null,
            'status' => 'new',
            'tax_rate' => (float) EventSetting::get('tax_rate'),
            'deposit_percentage' => (float) EventSetting::get('deposit_percentage'),
            'valid_until' => now()->addDays((int) EventSetting::get('quote_validity_days')),
            'locale' => app()->getLocale(),
            'tracking' => array_filter($v['tracking'] ?? []) ?: null,
        ]);

        foreach (Venue::whereIn('id', array_unique($v['venue_ids']))->get() as $venue) {
            $quote->quoteVenues()->create(['venue_id' => $venue->id, 'price' => $venue->price]);
        }

        $formula = EventMenuFormula::with('menuCategories')->findOrFail($v['menu_formula_id']);
        // On n'accepte que des plats de la formule choisie.
        $choices = EventMenuItem::whereIn('id', array_unique($v['menu_choices'] ?? []))
            ->whereIn('menu_category_id', $formula->menuCategories->pluck('id'))
            ->get();
        $supplements = (float) $choices->sum('supplement_price') * $guests;

        $quoteMenu = $quote->quoteMenus()->create([
            'menu_formula_id' => $formula->id,
            'guest_count' => $guests,
            'price_per_person' => $formula->price_per_person,
            'supplements_total' => $supplements,
            'total' => (float) $formula->price_per_person * $guests + $supplements,
        ]);
        foreach ($choices as $item) {
            $quoteMenu->quoteMenuChoices()->create(['menu_item_id' => $item->id]);
        }

        foreach (collect($v['drinks'] ?? [])->unique('id') as $line) {
            $option = DrinkOption::findOrFail($line['id']);
            // À la bouteille : quantité choisie. Sinon : forfait à volonté × convives.
            if ($option->unit_type === 'bottle') {
                $price = (float) $option->price_per_unit;
                $qty = (int) $line['quantity'];
                $mode = 'per_unit';
            } else {
                $price = (float) ($option->price_all_in ?? $option->price_per_unit);
                $qty = $guests;
                $mode = 'all_in';
            }
            $quote->quoteDrinks()->create([
                'drink_option_id' => $option->id,
                'quantity' => $qty,
                'price' => $price,
                'pricing_mode' => $mode,
                'total' => $price * $qty,
            ]);
        }

        foreach (collect($v['extras'] ?? [])->unique('id') as $line) {
            $item = ExtraItem::findOrFail($line['id']);
            $qty = match ($item->price_type) {
                'per_person' => $guests,
                'per_unit' => (int) $line['quantity'],
                default => 1,
            };
            $quote->quoteExtras()->create([
                'extra_item_id' => $item->id,
                'quantity' => $qty,
                'price' => $item->price,
                'total' => (float) $item->price * $qty,
            ]);
        }

        return $quote->calculateTotals();
    }
}
