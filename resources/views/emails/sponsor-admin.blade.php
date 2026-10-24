@php
    use App\Models\SponsorApplication;
    $a = $application;
    $rows = [
        'Activité principale' => $a->activity,
        'À promouvoir pendant le festival' => $a->promote,
        'À vendre sur place' => $a->sell,
        'Demandes particulières' => $a->message,
    ];
@endphp
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="utf-8"><title>Festival 1912</title></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:Arial,Helvetica,sans-serif;color:#292524;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f5f5f4;padding:24px 0;">
<tr><td align="center">
<table role="presentation" width="640" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;">
    <tr>
        <td style="background:#0c0a09;color:#f5efcf;padding:22px 28px;border-bottom:3px solid #c8102e;">
            <div style="font-size:19px;">Festival 1912 — {{ SponsorApplication::TYPES[$a->participation_type] ?? $a->participation_type }}</div>
            <div style="margin-top:4px;font-size:13px;color:#a8a29e;">{{ $a->created_at->format('d/m/Y H:i') }} — formulaire en {{ strtolower(SponsorApplication::LOCALE_LABELS[$a->locale] ?? $a->locale) }}</div>
        </td>
    </tr>
    <tr>
        <td style="padding:22px 28px;font-size:15px;line-height:1.8;">
            <strong>{{ $a->name }}</strong>@if($a->organization) — {{ $a->organization }}@endif<br>
            Téléphone : <a href="tel:{{ preg_replace('/[^\d+]/', '', $a->phone) }}" style="color:#c8102e;font-weight:bold;">{{ $a->phone }}</a><br>
            E-mail : <a href="mailto:{{ $a->email }}" style="color:#c8102e;">{{ $a->email }}</a><br>
            @foreach($rows as $label => $value)
                @if(filled($value))
                    <br><span style="color:#78716c;font-size:13px;">{{ $label }}</span><br>{!! nl2br(e($value)) !!}<br>
                @endif
            @endforeach
        </td>
    </tr>
    <tr>
        <td style="padding:0 28px 26px;">
            <a href="{{ $adminUrl }}" style="display:inline-block;background:#c8102e;color:#ffffff;text-decoration:none;padding:11px 22px;font-size:14px;">Ouvrir dans l'admin</a>
        </td>
    </tr>
</table>
</td></tr>
</table>
</body>
</html>
