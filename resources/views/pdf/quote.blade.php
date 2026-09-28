@php
    $t = fn (string $key, array $replace = []) => __('simulator.pdf.'.$key, $replace, $l);
    $money = fn ($v) => number_format((float) $v, 2, ',', '.').' €';
    $name = fn ($model, $attr = 'name') => $model ? $model->tr($attr, $l) : '—';
@endphp
<!DOCTYPE html>
<html lang="{{ $l }}">
<head>
<meta charset="utf-8">
<title>{{ $quote->quote_number }}</title>
<style>
    @page { margin: 0; }
    * { box-sizing: border-box; }
    body { margin: 0; font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #292524; line-height: 1.5; }
    .head { background: #0c0a09; color: #f5efcf; padding: 26px 40px 22px; }
    .head table { width: 100%; border-collapse: collapse; }
    .brand { font-size: 22px; letter-spacing: 3px; color: #f5efcf; }
    .brand-sub { font-size: 9px; color: #a8a29e; margin-top: 4px; line-height: 1.6; }
    .doc { text-align: right; vertical-align: top; }
    .doc-title { font-size: 17px; letter-spacing: 3px; color: #d9551f; font-weight: bold; }
    .doc-meta { font-size: 9px; color: #d6d3d1; line-height: 1.8; }
    .rule { height: 3px; background: #d9551f; }
    .page { padding: 24px 40px 30px; }
    .cols { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    .cols td { width: 50%; vertical-align: top; padding-right: 16px; }
    h3 { font-size: 9.5px; text-transform: uppercase; letter-spacing: 1.5px; color: #d9551f; margin: 0 0 6px; padding-bottom: 4px; border-bottom: 1px solid #e7e5e4; }
    .row { margin-bottom: 2px; }
    .lbl { color: #78716c; }
    .notice { background: #faf6ec; border-left: 3px solid #d9551f; padding: 10px 14px; margin: 6px 0 18px; color: #57534e; }
    .section { margin-top: 16px; }
    table.lines { width: 100%; border-collapse: collapse; }
    table.lines th { text-align: left; font-size: 8.5px; text-transform: uppercase; letter-spacing: 1px; color: #78716c; border-bottom: 1px solid #d6d3d1; padding: 5px 6px; }
    table.lines td { padding: 6px; border-bottom: 1px solid #f0efee; vertical-align: top; }
    .r { text-align: right; }
    .muted { color: #78716c; font-size: 9.5px; }
    .totals { width: 260px; margin-left: auto; margin-top: 16px; border-collapse: collapse; }
    .totals td { padding: 4px 6px; }
    .totals .grand td { border-top: 2px solid #0c0a09; font-size: 13px; font-weight: bold; padding-top: 8px; }
    .coming { margin-top: 22px; background: #0c0a09; color: #f5efcf; padding: 14px 18px; text-align: center; }
    .coming strong { color: #f5efcf; font-size: 12px; }
    .foot { margin-top: 26px; padding-top: 10px; border-top: 1px solid #e7e5e4; text-align: center; font-size: 8.5px; color: #a8a29e; }
</style>
</head>
<body>
<div class="head">
    <table><tr>
        <td>
            <div class="brand">ANTIKA</div>
            <div class="brand-sub">
                {{ $company['address'] }}<br>
                {{ $company['phone'] }} · {{ $company['email'] }}
            </div>
        </td>
        <td class="doc">
            <div class="doc-title">{{ $showPrices ? $t('quote') : $t('request') }}</div>
            <div class="doc-meta">
                {{ $t('number') }} {{ $quote->quote_number }}<br>
                {{ $t('date') }} : {{ $quote->created_at->format('d/m/Y') }}
                @if($showPrices && $quote->valid_until)<br>{{ $t('valid_until') }} : {{ $quote->valid_until->format('d/m/Y') }}@endif
            </div>
        </td>
    </tr></table>
</div>
<div class="rule"></div>

<div class="page">
    <table class="cols"><tr>
        <td>
            <h3>{{ $t('client') }}</h3>
            <div class="row"><span class="lbl">{{ $t('name') }} :</span> {{ $quote->customer->full_name }}</div>
            <div class="row"><span class="lbl">{{ $t('email') }} :</span> {{ $quote->customer->email }}</div>
            <div class="row"><span class="lbl">{{ $t('phone') }} :</span> {{ $quote->customer->phone ?: '—' }}</div>
            @if($quote->customer->company)
                <div class="row"><span class="lbl">{{ $t('company') }} :</span> {{ $quote->customer->company }}</div>
            @endif
        </td>
        <td>
            <h3>{{ $t('event') }}</h3>
            <div class="row"><span class="lbl">{{ $t('type') }} :</span> {{ $name($quote->eventType) }}</div>
            <div class="row"><span class="lbl">{{ $t('date') }} :</span> {{ $quote->event_date?->format('d/m/Y') ?? '—' }}</div>
            <div class="row"><span class="lbl">{{ $t('slot') }} :</span> {{ $quote->event_time_slot ? __('simulator.slots.'.$quote->event_time_slot, [], $l) : '—' }}</div>
            <div class="row"><span class="lbl">{{ $t('guests') }} :</span> {{ $t('persons', ['count' => $quote->guestCount()]) }}</div>
            @if($quote->child_menu)
                <div class="row">{{ $t('child_menu') }}</div>
            @endif
        </td>
    </tr></table>

    @unless($showPrices)
        <div class="notice">{{ $t('notice') }}</div>
    @endunless

    @if($quote->quoteVenues->isNotEmpty())
        <div class="section">
            <h3>{{ $t('venues') }}</h3>
            <table class="lines">
                @foreach($quote->quoteVenues as $line)
                    <tr>
                        <td>{{ $name($line->venue) }}</td>
                        @if($showPrices)<td class="r">{{ $money($line->price) }}</td>@endif
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    @if($quote->quoteMenus->isEmpty())
        <div class="section">
            <h3>{{ $t('menu') }}</h3>
            <table class="lines"><tr><td>{{ $t('no_catering') }}</td></tr></table>
        </div>
    @endif
    @foreach($quote->quoteMenus as $menu)
        <div class="section">
            <h3>{{ $t('menu') }}</h3>
            <table class="lines">
                <tr>
                    <td>
                        <strong>{{ $name($menu->menuFormula) }}</strong>
                        @if($menu->quoteMenuChoices->isNotEmpty())
                            <div class="muted">{{ $t('dishes') }} : {{ $menu->quoteMenuChoices->map(fn ($c) => $name($c->menuItem))->join(' · ') }}</div>
                        @endif
                    </td>
                    <td class="r">{{ $t('persons', ['count' => $menu->guest_count]) }}</td>
                    @if($showPrices)
                        <td class="r">{{ $money($menu->price_per_person) }} {{ $t('per_person') }}</td>
                        <td class="r">{{ $money($menu->total) }}@if((float) $menu->supplements_total > 0)<div class="muted">{{ $t('supplements') }} {{ $money($menu->supplements_total) }}</div>@endif</td>
                    @endif
                </tr>
            </table>
        </div>
    @endforeach

    @if($quote->quoteDrinks->isNotEmpty())
        <div class="section">
            <h3>{{ $t('drinks') }}</h3>
            <table class="lines">
                @foreach($quote->quoteDrinks as $line)
                    <tr>
                        <td>{{ $name($line->drinkOption) }} <span class="muted">({{ $line->pricing_mode === 'all_in' ? $t('all_in') : $t('per_bottle') }})</span></td>
                        <td class="r">{{ $t('quantity') }} {{ $line->quantity }}</td>
                        @if($showPrices)<td class="r">{{ $money($line->total) }}</td>@endif
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    @if($quote->quoteExtras->isNotEmpty())
        <div class="section">
            <h3>{{ $t('extras') }}</h3>
            <table class="lines">
                @foreach($quote->quoteExtras as $line)
                    <tr>
                        <td>{{ $name($line->extraItem) }}</td>
                        <td class="r">{{ $t('quantity') }} {{ $line->quantity }}</td>
                        @if($showPrices)<td class="r">{{ $money($line->total) }}</td>@endif
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    @if($quote->dietary_requirements)
        <div class="section"><h3>{{ $t('dietary') }}</h3><div>{{ $quote->dietary_requirements }}</div></div>
    @endif
    @if($quote->special_requests)
        <div class="section"><h3>{{ $t('requests') }}</h3><div>{{ $quote->special_requests }}</div></div>
    @endif

    @if($showPrices)
        <table class="totals">
            <tr><td class="lbl">{{ $t('subtotal') }}</td><td class="r">{{ $money($quote->subtotal) }}</td></tr>
            <tr><td class="lbl">{{ $t('vat', ['rate' => rtrim(rtrim(number_format((float) $quote->tax_rate, 2, ',', ''), '0'), ',')]) }}</td><td class="r">{{ $money($quote->tax_amount) }}</td></tr>
            <tr class="grand"><td>{{ $t('total') }}</td><td class="r">{{ $money($quote->total) }}</td></tr>
            <tr><td class="lbl">{{ $t('deposit', ['rate' => rtrim(rtrim(number_format((float) $quote->deposit_percentage, 2, ',', ''), '0'), ',')]) }}</td><td class="r">{{ $money($quote->deposit_amount) }}</td></tr>
        </table>
    @else
        <div class="coming"><strong>{{ $t('coming_title') }}</strong><br>{{ $t('coming_text') }}</div>
    @endif

    <div class="foot">
        {{ $showPrices ? $t('footer_quote') : $t('footer_request') }}<br>
        {{ $company['name'] }} — {{ $company['address'] }} — {{ $company['phone'] }} — {{ $company['email'] }}
    </div>
</div>
</body>
</html>
