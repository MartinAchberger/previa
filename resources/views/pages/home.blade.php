@extends('layouts.app')

@section('title', 'PREVIA - Profesionálna vlasová kozmetika z Talianska')

@section('content')

@php
    $featured = $heroProduct ?? $topProducts->first();
@endphp

<section class="hero">
    <div class="hero-l">
        <div class="eyebrow">Profesionálna starostlivosť o vlasy · Made in Italy</div>
        <h1 class="h1">Menej chémie.<br><em>Viac prírody</em><br><em>a výsledkov.</em></h1>
        <p class="lede">Každá receptúra PREVIA vzniká s dôrazom na čisté zloženie, profesionálny výkon a rešpekt k vlasom aj planéte.</p>
        <div class="hero-cta">
            <a href="{{ route('shop.index') }}" class="btn">Vstúpiť do eshopu</a>
            <a href="{{ route('b2b.login') }}" class="btn btn-line">Pre salóny ›</a>
        </div>
        <div class="hero-spec">
            <div>Aktívne látky<strong>Organické rastlinné extrakty</strong></div>
            <div>Vegan<strong>Áno</strong></div>
            <div>Pôvod<strong>Taliansko</strong></div>
        </div>
    </div>
    <div class="hero-r hero-r--photo">
        <img src="{{ asset('images/redesign/hero.jpg') }}" alt="PREVIA - profesionálna vlasová kozmetika" loading="eager">
        @if($featured)
            <div class="hero-callout">
                <div class="meta">Novinka · {{ now()->format('m / y') }}</div>
                <div class="name">{{ $featured->name }}</div>
            </div>
        @endif
    </div>
</section>

<section class="strip">
    @foreach([
        ['n' => 'i.',   't' => 'Vyrobené v Taliansku · profesionálna vlasová kozmetika z prémiových ingrediencií'],
        ['n' => 'ii.',  't' => 'Až 97 % prírodných ingrediencií · pre zdravšie vlasy aj pokožku hlavy'],
        ['n' => 'iii.', 't' => 'Vegan & cruelty-free · udržateľnosť a ekologické obaly'],
        ['n' => 'iv.',  't' => 'Čisté receptúry · bez agresívnych sulfátov, parabénov a silikónov'],
    ] as $s)
        <div class="strip-it"><div class="n">{{ $s['n'] }}</div><div class="t">{{ $s['t'] }}</div></div>
    @endforeach
</section>

<section class="products">
    <div class="section-head">
        <h2 class="h2"><em>Začnite svoju cestu</em><br>ku krajším vlasom.</h2>
        <a href="{{ route('shop.index') }}" class="section-sub" style="text-decoration:none">Zobraziť všetky produkty →</a>
    </div>
    <div class="grid-4">
        @foreach($topProducts as $p)
            @include('partials.product-card', ['p' => $p])
        @endforeach
    </div>
</section>

<section class="img-band">
    <div class="img-band-it"><img src="{{ asset('images/redesign/texture-foam.jpg') }}" alt="Textúra peny" loading="lazy"></div>
    <div class="img-band-it"><img src="{{ asset('images/redesign/band-2.jpg') }}" alt="Starostlivosť o vlasy" loading="lazy"></div>
    <div class="img-band-it"><img src="{{ asset('images/redesign/band-bottles.jpg') }}" alt="PREVIA produkty" loading="lazy"></div>
</section>

{{--
<section class="diag" id="diag">
    <div class="diag-l">
        <div class="eyebrow">Diagnostika · 90 sekúnd</div>
        <h2 class="h2">Nepoznáte svoje vlasy<br>tak dobre <em>ako my.</em></h2>
        <p class="lede">5 otázok. Naša knižnica formulácií navrhne rituál presne pre váš typ vlasov, pokožku a životný štýl. Bez registrácie.</p>
        <button class="btn" style="align-self:flex-start">Začať diagnostiku →</button>
    </div>
    <div class="diag-r">
        <div class="diag-q">
            <div class="step active"><div class="n">01</div><div class="t">Aký typ vlasov máte?</div><div class="a">→</div></div>
            <div class="step"><div class="n">02</div><div class="t">Najväčšia obava</div><div class="a">○</div></div>
            <div class="step"><div class="n">03</div><div class="t">Frekvencia umývania</div><div class="a">○</div></div>
            <div class="step"><div class="n">04</div><div class="t">Farbenie a chemické úpravy</div><div class="a">○</div></div>
            <div class="step"><div class="n">05</div><div class="t">Cieľ - čo chcete dosiahnuť</div><div class="a">○</div></div>
        </div>
    </div>
</section>
--}}

<section class="lines">
    <div class="section-head">
        <h2 class="h2">Naše<br><em>kolekcie.</em></h2>
        <a href="{{ route('shop.index') }}" class="section-sub" style="text-decoration:none">Zobraziť všetkých {{ $lines->count() }} kolekcií →</a>
    </div>
    @foreach($lines->take(8)->chunk(4) as $chunk)
        <div class="lines-grid" @if(!$loop->first) style="border-top:none" @endif>
            @foreach($chunk as $l)
                <a href="{{ route('shop.index', ['line' => $l->slug]) }}" class="line-it" style="text-decoration:none;color:inherit">
                    <div class="line-it-top">
                        <span class="num">línia {{ $l->code }}</span>
                        <span class="arrow">→</span>
                    </div>
                    <div class="nm">{{ $l->name }}<em>{{ $l->eyebrow }}</em></div>
                    <div class="ds">{{ $l->description }}</div>
                </a>
            @endforeach
        </div>
    @endforeach
</section>

<section class="cta-quiz">
    <div class="cta-quiz-l">
        <div class="eyebrow">Profesionálna diagnostika vlasov</div>
        <h2 class="h2">Nájdite rutinu, ktorú vaše<br><em>vlasy skutočne potrebujú.</em></h2>
        <p>Vyplňte diagnostiku za menej ako 60 sekúnd a získajte odporúčanie kolekcie PREVIA presne podľa typu vlasov, pokožky hlavy a vašich potrieb.</p>
        <div class="cta-quiz-cta">
            <a href="{{ route('quiz.show') }}" class="btn btn-light">Začať diagnostiku →</a>
            <span class="cta-quiz-meta">Menej ako 60 sekúnd</span>
        </div>
    </div>
    <div class="cta-quiz-r cta-quiz-r--photo">
        <img src="{{ asset('images/redesign/texture-oil.jpg') }}" alt="Diagnostika vlasov PREVIA" loading="lazy">
    </div>
</section>

<section class="claims-banner">
    <img src="{{ asset('images/redesign/banner.jpg') }}" alt="PREVIA" loading="lazy">
    <div class="claims-banner-in">
        <div class="eyebrow">Previa · Made in Italy</div>
        <h2 class="claims-head">Zdravé vlasy začínajú<br>zdravou <em>pokožkou hlavy.</em></h2>
    </div>
</section>

<section class="b2b-band b2b-band--light">
    <div class="b2b-band-l">
        <div class="eyebrow">Pre salóny</div>
        <h2 class="h2">Ste kaderník<br><em>alebo majiteľ salónu?</em></h2>
        <p>Pridajte sa k sieti profesionálnych partnerov PREVIA a získajte prístup k prémiovej talianskej vlasovej kozmetike, odbornému vzdelávaniu a individuálnej podpore pre rozvoj vášho salónu.</p>
        <a href="{{ route('b2b.register') }}" class="btn" style="align-self:flex-start;text-decoration:none">Získať prístup pre salón →</a>
    </div>
    <div class="b2b-band-r">
        <div class="b2b-li"><div class="n">01</div><div><div class="t">Prémiová značka</div><div class="ds">Ponúknite svojim klientom profesionálnu taliansku vlasovú kozmetiku s dôrazom na prírodné zloženie a výsledky.</div></div></div>
        <div class="b2b-li"><div class="n">02</div><div><div class="t">Odborné vzdelávanie</div><div class="ds">Pravidelné školenia, workshopy a technická podpora, ktoré vám pomôžu naplno využiť potenciál produktov PREVIA.</div></div></div>
        <div class="b2b-li"><div class="n">03</div><div><div class="t">Marketingová podpora</div><div class="ds">Propagačné materiály, merchandising a podpora pri budovaní úspešného salónu.</div></div></div>
        <div class="b2b-li"><div class="n">04</div><div><div class="t">Individuálny prístup</div><div class="ds">Osobný obchodný zástupca, odborné poradenstvo a partnerstvo, na ktoré sa môžete spoľahnúť.</div></div></div>
    </div>
</section>

<section class="cta-philosophy">
    <div class="cta-philosophy-l">
        <div class="eyebrow">Filozofia značky</div>
        <h2 class="h2">Skutočná starostlivosť.<br><em>Začína tam, kde ju nevidíte.</em></h2>
        <p>PREVIA verí, že zdravé a krásne vlasy sú výsledkom zdravej pokožky hlavy. Preto vytvára profesionálnu vlasovú starostlivosť, ktorá rešpektuje prirodzenú rovnováhu vlasov, spája účinné aktívne látky s vysokým podielom prírodných ingrediencií a premieňa každodennú rutinu na príjemný zmyslový zážitok.</p>
        <div class="cta-philosophy-pillars">
            <div><strong>Rovnováha</strong><span>Rešpektujeme prirodzenú rovnováhu vlasov aj pokožky hlavy.</span></div>
            <div><strong>Inovácie</strong><span>Moderné aktívne látky a profesionálne formulácie pre viditeľné a dlhodobé výsledky.</span></div>
            <div><strong>Zmysly</strong><span>Jedinečné vône a príjemné textúry premieňajú každé umývanie vlasov na relaxačný rituál.</span></div>
            <div><strong>Čistota</strong><span>Premyslené receptúry s vysokým podielom prírodných ingrediencií a dôrazom na šetrnú starostlivosť.</span></div>
        </div>
        <a href="{{ route('philosophy.show') }}" class="btn">Objaviť filozofiu →</a>
    </div>
    <div class="cta-philosophy-r cta-philosophy-r--photo">
        <img src="{{ asset('images/redesign/texture-cream.jpg') }}" alt="Textúra PREVIA" loading="lazy">
    </div>
</section>

{{-- Newsletter section dočasne skrytá - pridať keď bude funkčný subscribe endpoint --}}

@endsection
