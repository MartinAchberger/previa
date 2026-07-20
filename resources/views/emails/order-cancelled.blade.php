@extends('emails.layout')
@section('title', 'Objednávka ' . $order->order_number . ' zrušená')
@section('body')
    <p style="margin:0 0 16px;">Dobrý deň {{ $order->customer_name }},</p>
    <p style="margin:0 0 16px;">Vaša objednávka <strong>{{ $order->order_number }}</strong> bola zrušená.</p>

    @if ($order->payment_status === 'refunded' || $order->refunded_at)
        <p style="margin:0 0 16px;">Uhradenú sumu <strong>{{ $order->totalFormatted() }}</strong> Vám vraciame späť na účet, z ktorého bola platba realizovaná. Pripísanie zvyčajne trvá niekoľko pracovných dní.</p>
    @elseif ($order->payment_method === 'cod')
        <p style="margin:0 0 16px;">Objednávka bola na dobierku, takže z Vašej strany nie je potrebné nič ďalej riešiť.</p>
    @endif

    <p style="margin:16px 0 0;">Ak zrušenie nebolo na váš podnet alebo máte otázky, kontaktujte nás na e-mailovej adrese {{ config('mail.contact_address') }}.</p>
    <p style="margin:16px 0 0;">S pozdravom,<br>tím PREVIA</p>
@endsection
