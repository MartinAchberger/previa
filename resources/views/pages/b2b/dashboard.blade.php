@extends('layouts.app')

@section('title', 'Portál pre salóny - ' . $b2b->salon_name)

@section('content')

@include('partials.b2b-nav', ['active' => 'dashboard'])

<section class="b2b-hero">
    <div class="b2b-hero-l">
        <p class="eyebrow">Portál pre salóny</p>
        <h1>{{ $b2b->salon_name }}<br><em>−{{ $b2b->discount_pct }} % na celý katalóg.</em></h1>
        <p class="lede">Vitaj späť, {{ $b2b->contact_name }}. Tvoj veľkoobchodný cenník je aktívny - pri každom produkte v eshope vidíš automaticky upravenú cenu.</p>
        <div class="hero-cta">
            <a href="{{ route('shop.index') }}" class="btn">Otvoriť katalóg →</a>
            <a href="{{ route('b2b.orders') }}" class="btn btn-line">Moje objednávky</a>
        </div>
    </div>
    <div class="b2b-hero-r">
        <div class="b2b-stat"><div class="n">{{ $stats['orders_total'] }}</div><div class="l">Objednávok celkovo</div></div>
        <div class="b2b-stat"><div class="n">{{ $stats['orders_pending'] }}</div><div class="l">V spracovaní</div></div>
        <div class="b2b-stat"><div class="n">€{{ number_format($stats['spend_total'], 0, ',', ' ') }}</div><div class="l">Obrat celkom</div></div>
        <div class="b2b-stat"><div class="n">−{{ $b2b->discount_pct }}%</div><div class="l">Aktuálna zľava</div></div>
    </div>
</section>

<section class="b2b-recent">
    <div class="section-head">
        <h2 class="h2">Posledné objednávky</h2>
        <a href="{{ route('b2b.orders') }}" class="section-sub" style="text-decoration:none">Všetky →</a>
    </div>
    @if ($orders->isEmpty())
        <div class="b2b-empty">
            <p>Zatiaľ žiadne objednávky. <a href="{{ route('shop.index') }}">Začni nakupovať →</a></p>
        </div>
    @else
        <div class="b2b-orders-table">
            <div class="b2b-or-head">
                <div>Číslo</div>
                <div>Dátum</div>
                <div>Položky</div>
                <div>Suma</div>
                <div>Stav</div>
                <div></div>
            </div>
            @foreach ($orders as $o)
                <a href="{{ route('b2b.order.detail', $o->order_number) }}" class="b2b-or-row">
                    <div><strong>{{ $o->order_number }}</strong></div>
                    <div>{{ $o->created_at->format('j.n.Y') }}</div>
                    <div>{{ $o->items()->sum('qty') }} ks</div>
                    <div><strong>{{ $o->totalFormatted() }}</strong></div>
                    <div><span class="b2b-status b2b-status--{{ $o->status }}">{{ $o->statusLabel() }}</span></div>
                    <div style="text-align:right">→</div>
                </a>
            @endforeach
        </div>
    @endif
</section>

@endsection
