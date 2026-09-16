@php($t = fn ($k, $r = []) => __('simulator.email.'.$k, $r, $l))
<!DOCTYPE html>
<html lang="{{ $l }}">
<head><meta charset="utf-8"><title>{{ $t('heading') }}</title></head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:Arial,Helvetica,sans-serif;color:#292524;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f5f5f4;padding:32px 0;">
<tr><td align="center">
    <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;background:#ffffff;">
        <tr>
            <td style="background:#0c0a09;padding:34px 28px;text-align:center;border-bottom:3px solid #d9551f;">
                <div style="font-family:Georgia,serif;font-size:28px;letter-spacing:6px;color:#f5efcf;">ANTIKA</div>
                <div style="margin-top:10px;font-size:13px;letter-spacing:2px;text-transform:uppercase;color:#d9551f;">{{ $t('heading') }}</div>
            </td>
        </tr>
        <tr>
            <td style="padding:34px 32px 10px;font-size:15px;line-height:1.7;">
                <p style="margin:0 0 16px;font-size:17px;font-weight:bold;">{{ $t('hello', ['name' => $quote->customer->first_name]) }}</p>
                <p style="margin:0 0 14px;">{{ $t('thanks', ['number' => $quote->quote_number]) }}</p>
                <p style="margin:0 0 14px;">{{ $t('custom') }}</p>
                <p style="margin:0 0 14px;">{{ $t('attachment') }}</p>
            </td>
        </tr>
        @if($whatsapp)
        <tr>
            <td align="center" style="padding:6px 28px 26px;">
                <a href="https://wa.me/{{ $whatsapp }}" style="display:inline-block;padding:13px 26px;background:#25D366;color:#ffffff;text-decoration:none;font-weight:bold;font-size:14px;">{{ $t('whatsapp') }}</a>
            </td>
        </tr>
        @endif
        <tr>
            <td style="padding:0 32px 26px;font-size:14px;line-height:1.7;">
                <p style="margin:0;">{{ $t('regards') }}</p>
                <p style="margin:0;font-weight:bold;color:#d9551f;">{{ $t('team') }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding:18px 32px 26px;font-size:13px;color:#78716c;line-height:1.8;border-top:1px solid #e7e5e4;">
                {{ $t('phone') }} : {{ $phone }}<br>
                {{ $t('email') }} : {{ $email }}<br>
                <a href="{{ config('antika.url') }}" style="color:#d9551f;">{{ preg_replace('#^https?://#', '', config('antika.url')) }}</a>
            </td>
        </tr>
    </table>
</td></tr>
</table>
</body>
</html>
