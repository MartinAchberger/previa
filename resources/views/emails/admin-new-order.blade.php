@extends('emails.layout')
@section('title', 'Nová objednávka ' . $order->order_number)
@section('body')
    <p style="margin:0 0 16px;">Nová objednávka <strong>{{ $order->order_number }}</strong> ({{ $order->order_type === 'b2b' ? 'B2B salón' : 'B2C' }}).</p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 16px;font-size:13px;">
        <tr><td style="padding:3px 0;color:#8a857b;width:140px;">Zákazník</td><td style="padding:3px 0;">{{ $order->customer_name }}@if($order->company_name) · {{ $order->company_name }}@endif</td></tr>
        <tr><td style="padding:3px 0;color:#8a857b;">E-mail</td><td style="padding:3px 0;">{{ $order->customer_email }}</td></tr>
        <tr><td style="padding:3px 0;color:#8a857b;">Telefón</td><td style="padding:3px 0;">{{ $order->customer_phone }}</td></tr>
        <tr><td style="padding:3px 0;color:#8a857b;">Platba</td><td style="padding:3px 0;">{{ $order->paymentLabel() }} · {{ $order->payment_status }}</td></tr>
        <tr><td style="padding:3px 0;color:#8a857b;">Doprava</td><td style="padding:3px 0;">{{ $order->deliveryLabel() }} · {{ $order->shipping > 0 ? '€'.number_format($order->shipping, 2, ',', ' ') : 'zadarmo' }}</td></tr>
        <tr><td style="padding:3px 0;color:#8a857b;vertical-align:top;">Adresa</td><td style="padding:3px 0;">{{ $order->shipping_address }}, {{ $order->shipping_zip }} {{ $order->shipping_city }}@if($order->pickup_point_name && $order->pickup_point_name !== $order->shipping_address)<br>{{ $order->pickup_point_name }}@endif</td></tr>
        @if ($order->order_type === 'b2b' && $order->discount_pct > 0)
        <tr><td style="padding:3px 0;color:#8a857b;">Zľava salónu</td><td style="padding:3px 0;">−{{ $order->discount_pct }} % (−€{{ number_format($order->discount, 2, ',', ' ') }})</td></tr>
        @endif
        <tr><td style="padding:3px 0;color:#8a857b;">Spolu</td><td style="padding:3px 0;"><strong>{{ $order->totalFormatted() }}</strong></td></tr>
        @if ($order->notes)
        <tr><td style="padding:3px 0;color:#8a857b;vertical-align:top;">Poznámka</td><td style="padding:3px 0;font-style:italic;">{!! nl2br(e($order->notes)) !!}</td></tr>
        @endif
    </table>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 16px;border-collapse:collapse;">
        @foreach ($order->items as $item)
            <tr>
                <td style="padding:6px 0;border-bottom:1px solid #eee;font-size:13px;">{{ $item->qty }}× {{ $item->product_name }}@if($item->product_volume) · {{ $item->product_volume }}@endif</td>
                <td align="right" style="padding:6px 0;border-bottom:1px solid #eee;font-size:13px;">{{ $item->lineTotalFormatted() }}</td>
            </tr>
        @endforeach
    </table>
    <p style="margin:0;font-size:13px;"><a href="{{ route('platform.orders.view', $order->id) }}" style="color:#12110f;">Otvoriť v administrácii →</a></p>
@endsection
