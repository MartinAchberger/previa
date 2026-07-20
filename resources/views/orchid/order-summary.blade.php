@php /** @var \App\Models\Order $order */ @endphp
<div class="bg-white rounded shadow-sm p-4">
    <h5 style="font-size:14px;text-transform:uppercase;letter-spacing:0.18em;color:#6b6b66;margin-bottom:18px">Súhrn</h5>
    <div style="display:grid;grid-template-columns:140px 1fr;gap:8px 16px;font-size:14px;line-height:1.5">
        <div style="color:#6b6b66">Typ</div><div><strong>{{ strtoupper($order->order_type) }}</strong></div>
        @if ($order->b2bUser)
            <div style="color:#6b6b66">B2B salón</div><div><a href="{{ route('platform.b2b-users.edit', $order->b2bUser->id) }}">{{ $order->b2bUser->salon_name }}</a></div>
            <div style="color:#6b6b66">Zľava salónu</div><div>−{{ $order->b2bUser->discount_pct }} %</div>
        @endif
        <div style="color:#6b6b66">Zákazník</div><div>{{ $order->customer_name }}</div>
        <div style="color:#6b6b66">Email</div><div>{{ $order->customer_email }}</div>
        <div style="color:#6b6b66">Telefón</div><div>{{ $order->customer_phone }}</div>
        @if ($order->ico)<div style="color:#6b6b66">IČO</div><div>{{ $order->ico }}</div>@endif
        @if ($order->vat_id)<div style="color:#6b6b66">IČ DPH</div><div>{{ $order->vat_id }}</div>@endif
        <div style="color:#6b6b66">Adresa</div>
        <div>
            {{ $order->shipping_address }}<br>
            {{ $order->shipping_zip }} {{ $order->shipping_city }}<br>
            {{ $order->shipping_country }}
        </div>
        <div style="color:#6b6b66">Vytvorená</div><div>{{ $order->created_at?->format('j.n.Y · H:i') }}</div>
        <div style="color:#6b6b66">Platba</div>
        <div>
            <strong>{{ $order->paymentLabel() }}</strong>
            @if ($order->isPaid())
                · <span style="color:#047857">Zaplatené {{ $order->paid_at?->format('j.n.Y') }}</span>
            @elseif ($order->payment_method === 'cod')
                · <span style="color:#6b6b66">Bude uhradené pri prevzatí</span>
            @else
                · <span style="color:#6b6b66">Čaká na úhradu</span>
            @endif
        </div>
    </div>

    <hr style="margin:20px 0;border:none;border-top:1px solid #d8d8d2">

    <div style="display:grid;grid-template-columns:1fr auto;gap:8px;font-size:14px">
        @if ($order->discount > 0)
            <div style="color:#6b6b66">Pred zľavou</div><div>€{{ number_format($order->subtotal, 2, ',', ' ') }}</div>
            <div style="color:#6b6b66">Zľava (−{{ $order->discount_pct }} %)</div><div>−€{{ number_format($order->discount, 2, ',', ' ') }}</div>
        @else
            <div style="color:#6b6b66">Medzisúčet</div><div>€{{ number_format($order->subtotal, 2, ',', ' ') }}</div>
        @endif
        <div style="color:#6b6b66">Doprava</div><div>{{ $order->shipping > 0 ? '€'.number_format($order->shipping, 2, ',', ' ') : 'zadarmo' }}</div>
        <div style="font-weight:600;font-size:16px;border-top:1px solid #d8d8d2;padding-top:8px;margin-top:6px">Celkom</div>
        <div style="font-weight:600;font-size:18px;border-top:1px solid #d8d8d2;padding-top:8px;margin-top:6px">{{ $order->totalFormatted() }}</div>
    </div>
</div>
