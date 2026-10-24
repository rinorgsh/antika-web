@php
    $a = $application;
    $f = $s['form'];
    $rows = [
        $f['organization'] => $a->organization,
        $f['phone'] => $a->phone,
        $f['activity'] => $a->activity,
        $f['promote'] => $a->promote,
        $f['sell'] => $a->sell,
        $f['message'] => $a->message,
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ $l }}">
<head><meta charset="utf-8"><title>{{ $s['hero']['title'] }}</title></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:Arial,Helvetica,sans-serif;color:#292524;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f5f5f4;padding:24px 0;">
<tr><td align="center">
<table role="presentation" width="640" cellspacing="0" cellpadding="0" style="max-width:640px;background:#ffffff;">
    <tr>
        <td style="background:#0c0a09;color:#f5efcf;padding:24px 28px;border-bottom:3px solid #c8102e;">
            <div style="font-size:22px;font-family:Georgia,serif;">{{ $s['hero']['title'] }}</div>
            <div style="margin-top:4px;font-size:13px;color:#a8a29e;">{{ $s['hero']['subtitle'] }}</div>
        </td>
    </tr>
    <tr>
        <td style="padding:24px 28px 8px;font-size:15px;line-height:1.7;">
            <p style="margin:0 0 12px;">{{ str_replace(':name', $a->name, $s['email']['greeting']) }}</p>
            <p style="margin:0 0 12px;">{{ $s['email']['intro'] }}</p>
            @foreach(array_slice($s['notice']['lines'], 1) as $line)
                <p style="margin:0 0 12px;">{{ $line }}</p>
            @endforeach
        </td>
    </tr>
    <tr>
        <td style="padding:8px 28px 8px;">
            <div style="border:1px solid #e7e5e4;padding:16px 18px;font-size:14px;line-height:1.6;">
                <div style="font-size:12px;text-transform:uppercase;letter-spacing:1px;color:#78716c;margin-bottom:8px;">{{ $s['email']['recap'] }}</div>
                <strong>{{ $s['email']['type'] }} :</strong> {{ $f['types'][$a->participation_type]['label'] ?? $a->participation_type }}<br>
                @foreach($rows as $label => $value)
                    @if(filled($value))
                        <div style="margin-top:8px;"><span style="color:#78716c;">{{ $label }}</span><br>{!! nl2br(e($value)) !!}</div>
                    @endif
                @endforeach
            </div>
        </td>
    </tr>
    <tr>
        <td style="padding:16px 28px 28px;font-size:15px;line-height:1.7;">
            {{ $s['email']['signoff'] }}<br>
            <strong>{{ $s['email']['team'] }}</strong><br>
            <span style="color:#78716c;font-size:13px;font-style:italic;">{{ $s['tagline'] }}</span>
        </td>
    </tr>
</table>
</td></tr>
</table>
</body>
</html>
