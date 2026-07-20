@extends('layouts.app')

@section('title', 'Objednávka ' . $order->order_number . ' - Salón')

@section('content')

@include('partials.b2b-nav', ['active' => 'orders'])

<section class="shop-head">
    <div class="crumbs">
        <a href="{{ route('b2b.dashboard') }}" style="color:inherit;text-decoration:none">Salón</a>
        <span class="sep">/</span>
        <a href="{{ route('b2b.orders') }}" style="color:inherit;text-decoration:none">Objednávky</a>
        <span class="sep">/</span>
        <span>{{ $order->order_number }}</span>
    </div>
    <h1>{{ $order->order_number }}<br><em>· {{ $order->statusLabel() }}</em></h1>
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
            </div>
        @endif
    </div>
    <div class="oc-col">
        <h3>Platba a doprava</h3>
        <div class="oc-block">
            <div><strong>{{ $order->paymentLabel() }}</strong></div>
            @if ($order->isPaid())
                <div style="color:#047857;font-size:12px">✓ Zaplatené {{ $order->paid_at?->format('j.n.Y') }}</div>
            @elseif ($order->payment_method === 'cod')
                <div style="color:var(--mute);font-size:12px">Bude uhradené pri prevzatí.</div>
            @else
                <div style="color:var(--mute);font-size:12px">Čaká na úhradu.</div>
            @endif
            <div style="margin-top:12px">Kuriér · {{ $order->shipping > 0 ? '€'.number_format($order->shipping, 2, ',', ' ') : 'zadarmo' }}</div>
        </div>
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
    <h3>Položky</h3>
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
    <div class="oc-totals">
        @if ($order->discount > 0)
            <div class="oc-trow"><span>Pred zľavou</span><strong>€{{ number_format($order->subtotal, 2, ',', ' ') }}</strong></div>
            <div class="oc-trow oc-trow--disc"><span>Zľava pre salón (−{{ $order->discount_pct }} %)</span><strong>−€{{ number_format($order->discount, 2, ',', ' ') }}</strong></div>
            <div class="oc-trow"><span>Medzisúčet</span><strong>€{{ number_format($order->subtotal - $order->discount, 2, ',', ' ') }}</strong></div>
        @endif
        <div class="oc-trow"><span>Doprava</span><strong>{{ $order->shipping > 0 ? '€'.number_format($order->shipping, 2, ',', ' ') : 'zadarmo' }}</strong></div>
        <div class="oc-trow oc-trow--big"><span>Celkom</span><strong>{{ $order->totalFormatted() }}</strong></div>
    </div>
</section>

<section class="oc-cta">
    <a href="{{ route('b2b.orders') }}" class="btn btn-line">← Späť na objednávky</a>
    <a href="{{ route('shop.index') }}" class="btn">Objednať znova</a>
</section>

@endsection
