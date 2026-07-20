@extends('layouts.app')

@section('title', 'Objednávky - ' . $b2b->salon_name)

@section('content')

@include('partials.b2b-nav', ['active' => 'orders'])

<section class="shop-head">
    <div class="crumbs">
        <a href="{{ route('b2b.dashboard') }}" style="color:inherit;text-decoration:none">Salón</a>
        <span class="sep">/</span>
        <span>Objednávky</span>
    </div>
    <h1>História <em>objednávok.</em></h1>
    <div class="meta">
        <div class="ds">Všetky objednávky tvojho salónu. Klikni na riadok pre detail a faktúru.</div>
        <div class="cnt">{{ $orders->total() }} objednávok celkom</div>
    </div>
</section>

<section class="b2b-orders-page">
    @if ($orders->isEmpty())
        <div class="b2b-empty"><p>Žiadne objednávky. <a href="{{ route('shop.index') }}">Začni nakupovať →</a></p></div>
    @else
        <div class="b2b-orders-table">
            <div class="b2b-or-head">
                <div>Číslo</div>
                <div>Dátum</div>
                <div>Položky</div>
                <div>Suma</div>
                <div>Stav</div>
                <div>Platba</div>
                <div></div>
            </div>
            @foreach ($orders as $o)
                <a href="{{ route('b2b.order.detail', $o->order_number) }}" class="b2b-or-row b2b-or-row--7">
                    <div><strong>{{ $o->order_number }}</strong></div>
                    <div>{{ $o->created_at->format('j.n.Y') }}</div>
                    <div>{{ $o->items()->sum('qty') }} ks</div>
                    <div><strong>{{ $o->totalFormatted() }}</strong></div>
                    <div><span class="b2b-status b2b-status--{{ $o->status }}">{{ $o->statusLabel() }}</span></div>
                    <div style="font-size:12px;color:var(--mute)">{{ $o->paymentLabel() }}</div>
                    <div style="text-align:right">→</div>
                </a>
            @endforeach
        </div>
        <div style="margin-top:32px">{{ $orders->links() }}</div>
    @endif
</section>

@endsection
