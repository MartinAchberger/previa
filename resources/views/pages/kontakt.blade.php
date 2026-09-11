@extends('layouts.app')

@section('title', 'Kontakt - PREVIA')
@section('description', 'Kontakt PREVIA Slovensko: zákaznícka podpora info@previa.sk, odborné poradenstvo +421 903 430 149, osobný odber v Bratislave.')

@section('content')

<section class="shop-head">
    <div class="crumbs">
        <a href="{{ route('home') }}" style="color:inherit;text-decoration:none">PREVIA</a>
        <span class="sep">/</span>
        <span>Kontakt</span>
    </div>
    <h1>Sme tu<br><em>pre vás.</em></h1>
    <div class="meta">
        <div class="ds">Potrebujete pomôcť s výberom starostlivosti alebo máte otázku k objednávke? Ozvite sa nám.</div>
    </div>
</section>

<section class="contact-grid">
    <div class="contact-card">
        <div class="line">- 01</div>
        <h2>Zákaznícka podpora</h2>
        <p>Otázky k objednávkam, doručeniu či vráteniu tovaru nám napíšte e-mailom. Naša zákaznícka podpora vás bude kontaktovať s potrebnými informáciami.</p>
        <a href="mailto:info@previa.sk" class="contact-big">info@previa.sk</a>
    </div>
    <div class="contact-card">
        <div class="line">- 02</div>
        <h2>Odborné poradenstvo</h2>
        <p>Neviete, ktoré produkty sú vhodné pre vaše vlasy a pokožku hlavy? Zavolajte nám. Pomôžeme vám vybrať starostlivosť podľa vašich potrieb.</p>
        <a href="tel:+421903430149" class="contact-big">+421 903 430 149</a>
        <a href="{{ route('quiz.show') }}" class="contact-link">alebo vyplňte online diagnostiku →</a>
    </div>
    <div class="contact-card">
        <div class="line">- 03</div>
        <h2>Osobný odber</h2>
        <p>Objednávku si môžete vyzdvihnúť v pracovných dňoch od 8:00 do 15:30 na adrese:</p>
        <address class="info-address">Foxlog Warehouse<br>Stará Vajnorská 11<br>831 04 Bratislava<br>Slovenská republika</address>
        <p class="info-note">Pred vyzdvihnutím, prosím, počkajte na potvrdenie, že je vaša objednávka pripravená na odber.</p>
    </div>
</section>

<section class="contact-more">
    <a href="{{ route('delivery.show') }}" class="contact-more-it"><span>Doručenie a vrátenie</span><span aria-hidden="true">→</span></a>
    <a href="{{ route('faq.show') }}" class="contact-more-it"><span>Časté otázky</span><span aria-hidden="true">→</span></a>
    <a href="{{ route('b2b.register') }}" class="contact-more-it"><span>Spolupráca pre salóny</span><span aria-hidden="true">→</span></a>
</section>

@endsection
