<?php

namespace App\Http\Controllers;

use App\Mail\CallbackRequestMail;
use App\Models\CallbackRequest;
use App\Models\EventSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/** Formulaire court « Bel mij terug » des pages événements et d'annonces. */
class CallbackController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // Pot de miel anti-robots.
        if (filled($request->input('website'))) {
            return back();
        }

        $v = $request->validate([
            'name' => 'required|string|max:160',
            'phone' => ['required', 'string', 'max:50', function ($attribute, $value, $fail) {
                // Espaces, points et tirets acceptés : on ne compte que les chiffres.
                if (strlen(preg_replace('/\D/', '', (string) $value)) < 8) {
                    $fail(trans('site.callback.errors.phone'));
                }
            }],
            'email' => 'nullable|email|max:190',
            'event_type_id' => 'nullable|exists:event_types,id',
            'event_date' => 'nullable|date|after:today',
            'guest_count' => 'nullable|integer|min:1|max:2000',
            'message' => 'nullable|string|max:1000',
            'source_page' => 'nullable|string|max:255',
            'tracking' => 'nullable|array',
            'tracking.*' => 'nullable|string|max:255',
        ]);

        $v['phone'] = trim($v['phone']);
        $v['tracking'] = array_filter($v['tracking'] ?? []) ?: null;
        $v['locale'] = app()->getLocale();

        $callback = CallbackRequest::create($v);

        try {
            Mail::to(EventSetting::notifyRecipients())->send(new CallbackRequestMail($callback->load('eventType')));
        } catch (\Throwable $e) {
            Log::error('Envoi de la notification de rappel impossible', ['id' => $callback->id, 'error' => $e->getMessage()]);
        }

        return back()->with('callback_sent', true);
    }
}
