@extends('layouts.app')

@section('title', 'Cookies - PREVIA')
@section('description', 'Aké cookies používa web previa.sk, na čo slúžia a ako môžete zmeniť svoj súhlas.')

@section('content')

@php($cbid = config('services.cookiebot.cbid'))

<section class="shop-head">
    <div class="crumbs">
        <a href="{{ route('home') }}" style="color:inherit;text-decoration:none">PREVIA</a>
        <span class="sep">/</span>
        <span>Cookies</span>
    </div>
    <h1>Cookies<em>.</em></h1>
    <div class="meta">
        <div class="ds">Ako web previa.sk používa cookies a ako si nastavíte, s čím súhlasíte.</div>
    </div>
</section>

<section class="info-body info-body--single">
    <div class="info-content">
        <div class="info-block">
            <h2>Čo sú cookies</h2>
            <p>Cookies sú malé textové súbory, ktoré si prehliadač ukladá pri návšteve webu. Nevyhnutné cookies zabezpečujú základné fungovanie stránky, napríklad košík a pokladňu. Ostatné cookies (štatistické, marketingové) používame len s vaším súhlasom, ktorý môžete kedykoľvek zmeniť alebo odvolať.</p>
        </div>
        <div class="info-block">
            <h2>Nastavenia súhlasu</h2>
            <p>Svoj súhlas s cookies môžete kedykoľvek upraviť. Po kliknutí sa znova otvorí lišta s nastaveniami.</p>
            <button type="button" class="btn btn-line" onclick="if (window.Cookiebot) { Cookiebot.renew(); } else { alert('Nastavenia cookies momentálne nie sú dostupné.'); }">Zmeniť nastavenia cookies →</button>
        </div>
        <div class="info-block">
            <h2>Zoznam používaných cookies</h2>
            @if (filled($cbid))
                <script id="CookieDeclaration" src="https://consent.cookiebot.com/{{ $cbid }}/cd.js" type="text/javascript" async></script>
            @else
                <p class="info-note">Zoznam cookies sa načíta po aktivácii služby Cookiebot.</p>
            @endif
        </div>
    </div>
</section>

@endsection
