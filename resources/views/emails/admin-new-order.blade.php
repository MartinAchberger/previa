@extends('emails.layout')
@section('title', 'Nová objednávka ' . $order->order_number)
@section('body')
    <p style="margin:0 0 16px;">Nová objednávka <strong>{{ $order->order_number }}</strong> ({{ $order->order_type === 'b2b' ? 'B2B salón' : 'B2C' }}).</p>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 16px;font-size:13px;">
        <tr><td style="padding:3px 0;color:#8a857b;width:140px;">Zákazník</td><td style="padding:3px 0;">{{ $order->customer_name }}@if($order->company_name) · {{ $order->company_name }}@endif</td></tr>
        <tr><td style="padding:3px 0;color:#8a857b;">E-mail</td><td style="padding:3px 0;">{{ $order->customer_email }}</td></tr>
        <tr><td style="padding:3px 0;color:#8a857b;">Telefón</td><td style="padding:3px 0;">{{ $order->customer_phone }}</td></tr>
        <tr><td style="padding:3px 0;color:#8a857b;">Platba</td><td style="padding:3px 0;">{{ $order->paymentLabel() }} · {{ $order->payment_status }}</td></tr>
        <tr><td style="padding:3px 0;color:#8a857b;">Spolu</td><td style="padding:3px 0;"><strong>{{ $order->totalFormatted() }}</strong></td></tr>
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
