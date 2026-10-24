<?php

namespace App\Http\Controllers;

use App\Mail\SponsorApplicationAdminMail;
use App\Mail\SponsorApplicationCustomerMail;
use App\Models\EventSetting;
use App\Models\SponsorApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Festival 1912 (temporaire) : formulaire de participation sponsors / exposants / donateurs.
 * Page en français, néerlandais et albanais (?lang=fr|nl|sq), français par défaut.
 */
class SponsorController extends Controller
{
    public function index(Request $request): Response
    {
        $locale = $this->locale($request);

        return Inertia::render('Sponsors', [
            'lang' => $locale,
            'langs' => SponsorApplication::LOCALES,
            'strings' => trans('sponsors', [], $locale),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Pot de miel anti-robots.
        if (filled($request->input('website'))) {
            return back();
        }

        $locale = in_array($request->input('locale'), SponsorApplication::LOCALES, true) ? $request->input('locale') : 'fr';
        $err = fn (string $key) => trans("sponsors.errors.{$key}", [], $locale);

        $v = $request->validate([
            'participation_type' => ['required', Rule::in(array_keys(SponsorApplication::TYPES))],
            'name' => 'required|string|max:160',
            // Les donateurs peuvent être des particuliers : organisation facultative pour eux.
            'organization' => 'nullable|required_unless:participation_type,donor|string|max:190',
            'phone' => ['required', 'string', 'max:50', function ($attribute, $value, $fail) use ($err) {
                if (strlen(preg_replace('/\D/', '', (string) $value)) < 8) {
                    $fail($err('phone'));
                }
            }],
            'email' => 'required|email|max:190',
            'activity' => 'nullable|string|max:2000',
            'promote' => 'nullable|string|max:2000',
            'sell' => 'nullable|string|max:2000',
            'message' => 'nullable|string|max:2000',
        ], [
            'participation_type.*' => $err('type'),
            'name.*' => $err('name'),
            'organization.*' => $err('organization'),
            'phone.*' => $err('phone'),
            'email.*' => $err('email'),
            '*.max' => $err('too_long'),
        ]);

        $v['phone'] = trim($v['phone']);
        $v['email'] = strtolower(trim($v['email']));
        $v['locale'] = $locale;

        $application = SponsorApplication::create($v);

        try {
            Mail::to(EventSetting::notifyRecipients())->send(new SponsorApplicationAdminMail($application));
            Mail::to($application->email)->send(new SponsorApplicationCustomerMail($application));
        } catch (\Throwable $e) {
            Log::error('Envoi des e-mails Festival 1912 impossible', ['id' => $application->id, 'error' => $e->getMessage()]);
        }

        return back()->with('sponsor_sent', true);
    }

    /** ?lang= prioritaire et mémorisé pour la page ; sinon dernier choix, sinon français. */
    private function locale(Request $request): string
    {
        $requested = $request->query('lang');
        if (is_string($requested) && in_array($requested, SponsorApplication::LOCALES, true)) {
            $request->session()->put('sponsors_locale', $requested);
        }

        $locale = $request->session()->get('sponsors_locale', 'fr');

        return in_array($locale, SponsorApplication::LOCALES, true) ? $locale : 'fr';
    }
}
