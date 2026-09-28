@php
    /** @var \App\Models\Quote $quote */
    $quote = $getRecord()->loadMissing(\App\Models\Quote::FULL);
    $fr = fn ($m, $a = 'name') => $m ? $m->tr($a, 'fr') : '—';
    $money = fn ($v) => number_format((float) $v, 2, ',', '.').' €';
@endphp
<style>
    .ql { width: 100%; border-collapse: collapse; font-size: .875rem; }
    .ql td { padding: .55rem .75rem .55rem 0; vertical-align: top; border-bottom: 1px solid rgba(127,127,127,.18); }
    .ql .k { width: 6.5rem; color: rgb(156,163,175); }
    .ql .n { font-weight: 500; }
    .ql .d { color: rgb(156,163,175); }
    .ql .m { text-align: right; white-space: nowrap; font-variant-numeric: tabular-nums; padding-right: 0; }
    .ql tfoot td { border-bottom: 0; padding-top: .3rem; padding-bottom: .3rem; }
    .ql tfoot tr:first-child td { border-top: 2px solid rgba(127,127,127,.35); padding-top: .8rem; }
    .ql .r { text-align: right; color: rgb(156,163,175); }
    .ql .strong td { font-weight: 700; color: inherit; }
    @media (max-width: 640px) {
        .ql tbody tr { display: grid; grid-template-columns: 1fr auto; column-gap: .75rem; padding: .6rem 0; border-bottom: 1px solid rgba(127,127,127,.18); }
        .ql tbody td { border: 0; padding: 0; }
        .ql tbody .k { grid-column: 1 / -1; font-size: .7rem; text-transform: uppercase; letter-spacing: .08em; }
        .ql tbody .d { grid-column: 1; font-size: .8rem; }
        .ql tbody .m { grid-column: 2; grid-row: 2; }
    }
    .ql-notes { margin-top: 1.25rem; padding: 1rem; border-radius: .5rem; background: rgba(245,158,11,.1); color: rgb(245,158,11); line-height: 1.6; }
</style>
<table class="ql">
    <tbody>
        @foreach($quote->quoteVenues as $l)
            <tr><td class="k">Espace</td><td class="n">{{ $fr($l->venue) }}</td><td class="d"></td><td class="m">{{ $money($l->price) }}</td></tr>
        @endforeach
        @if($quote->quoteMenus->isEmpty())
            <tr><td class="k">Menu</td><td class="n">Sans traiteur (location de salle seule)</td><td class="d"></td><td class="m"></td></tr>
        @endif
        @foreach($quote->quoteMenus as $m)
            <tr>
                <td class="k">Menu</td>
                <td><span class="n">{{ $fr($m->menuFormula) }}</span>
                    @if($m->quoteMenuChoices->isNotEmpty())<div class="d">{{ $m->quoteMenuChoices->map(fn ($c) => $fr($c->menuItem))->join(' · ') }}</div>@endif
                </td>
                <td class="d">{{ $m->guest_count }} × {{ $money($m->price_per_person) }}@if((float) $m->supplements_total > 0)<br>+ suppléments {{ $money($m->supplements_total) }}@endif</td>
                <td class="m">{{ $money($m->total) }}</td>
            </tr>
        @endforeach
        @foreach($quote->quoteDrinks as $l)
            <tr><td class="k">Boisson</td><td class="n">{{ $fr($l->drinkOption) }}</td><td class="d">{{ $l->quantity }} × {{ $money($l->price) }} · {{ $l->pricing_mode === 'all_in' ? 'à volonté' : 'bouteille' }}</td><td class="m">{{ $money($l->total) }}</td></tr>
        @endforeach
        @foreach($quote->quoteExtras as $l)
            <tr><td class="k">Extra</td><td class="n">{{ $fr($l->extraItem) }}</td><td class="d">{{ $l->quantity }} × {{ $money($l->price) }}</td><td class="m">{{ $money($l->total) }}</td></tr>
        @endforeach
    </tbody>
    <tfoot>
        @php($pct = fn ($v) => rtrim(rtrim(number_format((float) $v, 2, ',', ''), '0'), ','))
        <tr><td colspan="3" class="r">Sous-total HTVA</td><td class="m">{{ $money($quote->subtotal) }}</td></tr>
        <tr><td colspan="3" class="r">TVA {{ $pct($quote->tax_rate) }} %</td><td class="m">{{ $money($quote->tax_amount) }}</td></tr>
        <tr class="strong"><td colspan="3" class="r">Total TVAC</td><td class="m">{{ $money($quote->total) }}</td></tr>
        <tr><td colspan="3" class="r">Acompte {{ $pct($quote->deposit_percentage) }} %</td><td class="m">{{ $money($quote->deposit_amount) }}</td></tr>
    </tfoot>
</table>

@if($quote->child_menu || $quote->dietary_requirements || $quote->special_requests)
    <div class="ql-notes">
        @if($quote->child_menu)<div><strong>Menu enfant souhaité</strong></div>@endif
        @if($quote->dietary_requirements)<div><strong>Allergies / régimes :</strong> {{ $quote->dietary_requirements }}</div>@endif
        @if($quote->special_requests)<div><strong>Souhaits :</strong> {{ $quote->special_requests }}</div>@endif
    </div>
@endif
