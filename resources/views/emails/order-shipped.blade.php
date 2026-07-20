@extends('emails.layout')
@section('title', 'Objednávka ' . $order->order_number . ' odoslaná')
@section('body')
    <p style="margin:0 0 16px;">Dobrý deň {{ $order->customer_name }},</p>
    <p style="margin:0 0 16px;">Vaša objednávka <strong>{{ $order->order_number }}</strong> bola odoslaná a je na ceste k Vám.</p>

    @if ($order->tracking_number)
        <p style="margin:0 0 6px;"><strong>Číslo zásielky:</strong> {{ $order->tracking_number }}</p>
    @endif
    @if ($order->tracking_link)
        <p style="margin:0 0 16px;"><a href="{{ $order->tracking_link }}" style="color:#12110f;">Sledovať zásielku →</a></p>
    @endif

    <p style="margin:16px 0 0;">Ak máte otázky, kontaktujte nás na e-mailovej adrese {{ config('mail.contact_address') }}.</p>
    <p style="margin:16px 0 0;">S pozdravom,<br>tím PREVIA</p>
@endsection
