@php
    $c = $callback;
    $lang = ['nl' => 'néerlandais', 'fr' => 'français', 'en' => 'anglais'][$c->locale] ?? $c->locale;
    $fromAds = collect($c->tracking ?? [])->keys()->contains(fn ($k) => str_ends_with($k, 'clid') || $k === 'gbraid' || $k === 'wbraid');
@endphp
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="utf-8"><title>Demande de rappel</title></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:Arial,Helvetica,sans-serif;color:#292524;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f5f5f4;padding:24px 0;">
<tr><td align="center">
<table role="presentation" width="640" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;">
    <tr>
        <td style="background:#0c0a09;color:#f5efcf;padding:22px 28px;border-bottom:3px solid #d9551f;">
            <div style="font-size:19px;">Un client demande à être rappelé</div>
            <div style="margin-top:4px;font-size:13px;color:#a8a29e;">{{ $c->created_at->format('d/m/Y H:i') }} — client en {{ $lang }}@if($fromAds) — via Google Ads @endif</div>
        </td>
    </tr>
    <tr>
        <td style="padding:22px 28px;font-size:15px;line-height:1.8;">
            <strong>{{ $c->name }}</strong><br>
            Téléphone : <a href="tel:{{ preg_replace('/[^\d+]/', '', $c->phone) }}" style="color:#d9551f;font-weight:bold;">{{ $c->phone }}</a><br>
            @if($c->email)E-mail : <a href="mailto:{{ $c->email }}" style="color:#d9551f;">{{ $c->email }}</a><br>@endif
            @if($c->eventType)Événement : {{ $c->eventType->tr('name', 'fr') }}<br>@endif
            @if($c->event_date)Date souhaitée : {{ $c->event_date->format('d/m/Y') }}<br>@endif
            @if($c->guest_count)Invités : {{ $c->guest_count }}<br>@endif
            @if($c->message)<br><em>« {{ $c->message }} »</em><br>@endif
            @if($c->source_page)<br><span style="color:#78716c;font-size:13px;">Page : {{ $c->source_page }}</span>@endif
        </td>
    </tr>
    <tr>
        <td style="padding:0 28px 26px;">
            <a href="{{ $adminUrl }}" style="display:inline-block;background:#d9551f;color:#ffffff;text-decoration:none;padding:11px 22px;font-size:14px;">Ouvrir dans l'admin</a>
        </td>
    </tr>
</table>
</td></tr>
</table>
</body>
</html>
