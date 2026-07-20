<!doctype html>
<html lang="sk">
<head>
    <meta charset="utf-8">
    <title>{{ $isProforma ? 'Proforma faktúra' : 'Faktúra' }} {{ $docNumber }}</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color:#1a1a1a; line-height:1.5;">
    <p>Dobrý deň {{ $order->customer_name }},</p>

    @if ($isProforma)
        <p>ďakujeme za Vašu objednávku <strong>{{ $order->order_number }}</strong>. V prílohe nájdete proforma faktúru s pokynmi na platbu.</p>
        <p><strong>Variabilný symbol:</strong> {{ $order->variableSymbol() }}<br>
           <strong>Suma:</strong> {{ $order->totalFormatted() }}</p>
        <p>Po prijatí platby Vám vystavíme riadnu faktúru a objednávku odošleme.</p>
    @else
        <p>v prílohe nájdete faktúru k Vašej objednávke <strong>{{ $order->order_number }}</strong>.</p>
        <p><strong>Suma:</strong> {{ $order->totalFormatted() }}<br>
           <strong>Spôsob platby:</strong> {{ $order->paymentLabel() }}</p>
        @if ($order->payment_method === 'transfer')
            <p>Faktúru uhraďte bankovým prevodom do dátumu splatnosti uvedeného na faktúre. Pre rýchlu platbu môžete použiť QR kód priamo na faktúre.</p>
            <p><strong>Variabilný symbol:</strong> {{ $order->variableSymbol() }}</p>
        @endif
    @endif

    <p>Ak máte otázky, neváhajte nás kontaktovať.</p>

    <p>S pozdravom,<br>tím PREVIA</p>
</body>
</html>
