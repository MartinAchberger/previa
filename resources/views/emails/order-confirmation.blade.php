@extends('emails.layout')
@section('title', 'Potvrdenie objednávky ' . $order->order_number)
@section('body')
    <p style="margin:0 0 16px;">Dobrý deň {{ $order->customer_name }},</p>
    <p style="margin:0 0 16px;">ďakujeme za Vašu objednávku <strong>{{ $order->order_number }}</strong>. Prijali sme ju a pracujeme na jej doručení.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0;border-collapse:collapse;">
        @foreach ($order->items as $item)
            <tr>
                <td style="padding:8px 0;border-bottom:1px solid #eee;font-size:13px;">
                    {{ $item->product_name }}@if($item->product_volume) · {{ $item->product_volume }}@endif<br>
                    <span style="color:#8a857b;">{{ $item->qty }} × {{ $item->unitPriceFormatted() }}</span>
                </td>
                <td align="right" style="padding:8px 0;border-bottom:1px solid #eee;font-size:13px;white-space:nowrap;">{{ $item->lineTotalFormatted() }}</td>
            </tr>
        @endforeach
        <tr>
            <td style="padding:8px 0;font-size:13px;color:#8a857b;">Doprava</td>
            <td align="right" style="padding:8px 0;font-size:13px;">{{ $order->shipping > 0 ? '€'.number_format($order->shipping, 2, ',', ' ') : 'zadarmo' }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;font-size:15px;font-weight:bold;">Spolu</td>
            <td align="right" style="padding:8px 0;font-size:15px;font-weight:bold;">{{ $order->totalFormatted() }}</td>
        </tr>
    </table>

    <p style="margin:0 0 6px;font-size:13px;"><strong>Spôsob platby:</strong> {{ $order->paymentLabel() }}</p>
    <p style="margin:0 0 16px;font-size:13px;"><strong>Doručenie:</strong> {{ $order->shipping_address }}, {{ $order->shipping_zip }} {{ $order->shipping_city }}</p>

    <p style="margin:16px 0 0;">Faktúra bude odoslaná e-mailom samostatne. Ak máte otázky, kontaktujte nás na e-mailovej adrese {{ config('mail.contact_address') }}.</p>
    <p style="margin:16px 0 0;">S pozdravom,<br>tím PREVIA</p>
@endsection
