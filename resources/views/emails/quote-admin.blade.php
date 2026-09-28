@php
    $c = $quote->customer;
    $money = fn ($v) => number_format((float) $v, 2, ',', '.').' €';
    $fr = fn ($m) => $m ? $m->tr('name', 'fr') : '—';
    $lang = ['nl' => 'néerlandais', 'fr' => 'français', 'en' => 'anglais'][$quote->locale] ?? $quote->locale;
@endphp
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="utf-8"><title>Nouvelle demande de devis</title></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:Arial,Helvetica,sans-serif;color:#292524;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f5f5f4;padding:24px 0;">
<tr><td align="center">
<table role="presentation" width="640" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;">
    <tr>
        <td style="background:#0c0a09;color:#f5efcf;padding:22px 28px;border-bottom:3px solid #d9551f;">
            <div style="font-size:19px;">Nouvelle demande de devis</div>
            <div style="margin-top:4px;font-size:13px;color:#a8a29e;">{{ $quote->quote_number }} — {{ $quote->created_at->format('d/m/Y H:i') }}</div>
        </td>
    </tr>
    <tr>
        <td style="padding:20px 28px;background:#faf6ec;font-size:14px;line-height:1.7;">
            <strong>{{ $c->full_name }}</strong> demande un devis pour <strong>{{ $quote->guestCount() }} personnes</strong>
            le <strong>{{ $quote->event_date?->format('d/m/Y') }}</strong>
            ({{ $quote->event_time_slot ? \App\Models\Quote::TIME_SLOTS[$quote->event_time_slot] ?? '' : '' }}, {{ $fr($quote->eventType) }}).<br>
            Estimation : <strong>{{ $money($quote->total) }} TVAC</strong> — client en {{ $lang }}.
        </td>
    </tr>
    <tr>
        <td style="padding:22px 28px;font-size:14px;line-height:1.8;">
            <div style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:#d9551f;margin-bottom:6px;">Contact</div>
            {{ $c->full_name }}@if($c->company) — {{ $c->company }}@endif<br>
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $c->phone) }}" style="color:#292524;">{{ $c->phone }}</a> ·
            <a href="mailto:{{ $c->email }}" style="color:#292524;">{{ $c->email }}</a>

            <div style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:#d9551f;margin:18px 0 6px;">Sélection</div>
            <strong>Espaces :</strong> {{ $quote->quoteVenues->map(fn ($l) => $fr($l->venue))->join(', ') ?: '—' }}<br>
            @forelse($quote->quoteMenus as $m)
                <strong>Menu :</strong> {{ $fr($m->menuFormula) }}
                @if($m->quoteMenuChoices->isNotEmpty()) ({{ $m->quoteMenuChoices->map(fn ($ch) => $fr($ch->menuItem))->join(', ') }})@endif<br>
            @empty
                <strong>Menu :</strong> sans traiteur (location de salle seule)<br>
            @endforelse
            <strong>Boissons :</strong> {{ $quote->quoteDrinks->map(fn ($l) => $fr($l->drinkOption).($l->pricing_mode === 'per_unit' ? ' ×'.$l->quantity : ''))->join(', ') ?: '—' }}<br>
            <strong>Extras :</strong> {{ $quote->quoteExtras->map(fn ($l) => $fr($l->extraItem))->join(', ') ?: '—' }}<br>
            @if($quote->child_menu)<strong>Menu enfant souhaité</strong><br>@endif
            @if($quote->dietary_requirements)<strong>Allergies / régimes :</strong> {{ $quote->dietary_requirements }}<br>@endif
            @if($quote->special_requests)<strong>Souhaits :</strong> {{ $quote->special_requests }}<br>@endif
            @if($quote->tracking)
                <div style="margin-top:12px;font-size:12px;color:#78716c;">Provenance : {{ collect($quote->tracking)->map(fn ($v, $k) => "$k=$v")->join(' · ') }}</div>
            @endif
        </td>
    </tr>
    <tr>
        <td align="center" style="padding:4px 28px 28px;">
            <a href="{{ $adminUrl }}" style="display:inline-block;padding:12px 24px;background:#d9551f;color:#ffffff;text-decoration:none;font-weight:bold;font-size:14px;">Ouvrir dans l'admin</a>
            <div style="margin-top:10px;font-size:12px;color:#a8a29e;">Le devis chiffré (PDF) est en pièce jointe. Répondre à ce mail écrit directement au client.</div>
        </td>
    </tr>
</table>
</td></tr>
</table>
</body>
</html>
