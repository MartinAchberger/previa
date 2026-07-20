@extends('layouts.app')

@section('title', 'Objednávka prijatá - PREVIA')

@section('content')

<section class="oc-hero">
    <div class="container-narrow">
        <div class="oc-tick">✓</div>
        <p class="eyebrow center">- Objednávka prijatá</p>
        <h1>{{ $order->order_number }}</h1>
        <p class="oc-lead">Ďakujeme. Tvoju objednávku sme prijali. Posielame potvrdenie na <strong>{{ $order->customer_email }}</strong> a do 24 hodín ti dáme vedieť o expedícii.</p>
    </div>
</section>

<section class="oc-grid">
    <div class="oc-col">
        @if ($order->hasSeparateBillingAddress())
            <h3>Fakturácia</h3>
            <div class="oc-block">
                @if ($order->company_name)<div>{{ $order->company_name }}</div>@endif
                @if ($order->ico)<div style="color:var(--mute);font-size:12px">IČO: {{ $order->ico }}@if ($order->vat_id) · IČ DPH: {{ $order->vat_id }}@endif</div>@endif
                <div>{{ $order->billingAddress() }}</div>
                <div>{{ $order->billingZip() }} {{ $order->billingCity() }}</div>
                <div>{{ $order->billingCountry() }}</div>
            </div>
            <h3 style="margin-top:32px">Doručenie</h3>
            <div class="oc-block">
                <div>{{ $order->customer_name }}</div>
                <div>{{ $order->shipping_address }}</div>
                <div>{{ $order->shipping_zip }} {{ $order->shipping_city }}</div>
                <div>{{ $order->shipping_country }}</div>
                <div style="margin-top:12px;color:var(--mute)">{{ $order->customer_email }} · {{ $order->customer_phone }}</div>
            </div>
        @else
            <h3>Doručenie a fakturácia</h3>
            <div class="oc-block">
                <div>{{ $order->customer_name }}</div>
                @if ($order->company_name)<div>{{ $order->company_name }}</div>@endif
                @if ($order->ico)<div style="color:var(--mute);font-size:12px">IČO: {{ $order->ico }}@if ($order->vat_id) · IČ DPH: {{ $order->vat_id }}@endif</div>@endif
                <div>{{ $order->shipping_address }}</div>
                <div>{{ $order->shipping_zip }} {{ $order->shipping_city }}</div>
                <div>{{ $order->shipping_country }}</div>
                <div style="margin-top:12px;color:var(--mute)">{{ $order->customer_email }} · {{ $order->customer_phone }}</div>
            </div>
        @endif
    </div>
    <div class="oc-col">
        <h3>Platba</h3>
        <div class="oc-block">
            <div><strong>{{ $order->paymentLabel() }}</strong></div>
            @if ($order->isPaid())
                <div style="color:#047857;margin-top:6px;font-size:12px">✓ Zaplatené {{ $order->paid_at?->format('j.n.Y · H:i') }}</div>
            @elseif ($order->payment_method === 'cod')
                <div style="color:var(--mute);margin-top:6px;font-size:12px">Bude uhradené pri prevzatí kuriérovi.</div>
            @else
                <div style="color:var(--mute);margin-top:6px;font-size:12px">Čaká na úhradu.</div>
            @endif
        </div>
        <h3 style="margin-top:32px">Doprava</h3>
        <div class="oc-block">Kuriér · {{ $order->shipping > 0 ? '€'.number_format($order->shipping, 2, ',', ' ') : 'zadarmo' }}</div>
    </div>
    <div class="oc-col">
        <h3>Stav objednávky</h3>
        <div class="oc-block">
            <div><strong>{{ $order->statusLabel() }}</strong></div>
            <div style="color:var(--mute);margin-top:6px;font-size:12px">Vytvorená {{ $order->created_at->format('j.n.Y · H:i') }}</div>
        </div>
    </div>
</section>

<section class="oc-items">
    <h3>Položky objednávky</h3>
    <div class="oc-list">
        @foreach ($order->items as $item)
            <div class="oc-li">
                <div class="oc-li-info">
                    <div class="oc-li-name">{{ $item->product_name }}</div>
                    <div class="oc-li-meta">{{ $item->product_line_label }} · {{ $item->product_volume }}</div>
                </div>
                <div class="oc-li-qty">{{ $item->qty }}×</div>
                <div class="oc-li-pr">€{{ number_format($item->unit_price, 2, ',', ' ') }}</div>
                <div class="oc-li-tot">{{ $item->lineTotalFormatted() }}</div>
            </div>
        @endforeach
    </div>
    @php
        $netSub = ($order->subtotal - $order->discount);
        $vatAmount = round($netSub - $netSub / (1 + \App\Models\Product::VAT_RATE), 2);
    @endphp
    <div class="oc-totals">
        @if ($order->discount > 0)
            <div class="oc-trow"><span>Pred zľavou</span><strong>€{{ number_format($order->subtotal, 2, ',', ' ') }}</strong></div>
            <div class="oc-trow oc-trow--disc"><span>Zľava pre salón (−{{ $order->discount_pct }} %)</span><strong>−€{{ number_format($order->discount, 2, ',', ' ') }}</strong></div>
            <div class="oc-trow"><span>Medzisúčet</span><strong>€{{ number_format($netSub, 2, ',', ' ') }}</strong></div>
        @else
            <div class="oc-trow"><span>Medzisúčet</span><strong>€{{ number_format($order->subtotal, 2, ',', ' ') }}</strong></div>
        @endif
        <div class="oc-trow" style="font-size:12px;color:var(--mute)"><span>z toho DPH 23 %</span><span>€{{ number_format($vatAmount, 2, ',', ' ') }}</span></div>
        <div class="oc-trow"><span>Doprava</span><strong>{{ $order->shipping > 0 ? '€'.number_format($order->shipping, 2, ',', ' ') : 'zadarmo' }}</strong></div>
        <div class="oc-trow oc-trow--big"><span>Celkom</span><strong>{{ $order->totalFormatted() }}</strong></div>
    </div>
</section>

<section class="oc-cta">
    <a href="{{ route('shop.index') }}" class="btn">Späť do eshopu</a>
    <a href="{{ route('home') }}" class="btn btn-line">Domov</a>
</section>

<script>
    // Clear cart after successful order
    if (window.PhCart) window.PhCart.clear();
    else localStorage.removeItem('ph_cart_v1');
</script>

@endsection
