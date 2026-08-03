@extends('layouts.app')

@section('title', 'Filozofia - PREVIA')
@section('description', 'PREVIA verí, že zdravé a krásne vlasy sú výsledkom zdravej pokožky hlavy. Objavte filozofiu profesionálnej talianskej vlasovej starostlivosti.')

@section('content')

<section class="j-feat j-feat--first">
    <div class="ph" style="background-image:url('{{ asset('images/redesign/filozofia1.jpg') }}')"></div>
    <div class="body">
        <div class="meta">01 — Filozofia</div>
        <h2>Skutočná starostlivosť.<br><em>Začína tam, kde ju nevidíte.</em></h2>
        <p>PREVIA verí, že zdravé a krásne vlasy sú výsledkom zdravej pokožky hlavy. Preto vytvára profesionálnu starostlivosť, ktorá rešpektuje prirodzenú rovnováhu vlasov, využíva silu prírodných ingrediencií a premieňa každodennú rutinu na príjemný zmyslový zážitok.</p>
    </div>
</section>

<section class="j-feat">
    <div class="ph" style="background-image:url('{{ asset('images/redesign/filozofia2.jpg') }}')"></div>
    <div class="body">
        <div class="meta">02 — Pokožka</div>
        <h2>Zdravé vlasy majú<br><em>pevné základy.</em></h2>
        <p>Každá kolekcia vzniká s dôrazom na zdravie pokožky hlavy. Vyvážená pokožka je základom silných, krásnych a prirodzene zdravých vlasov.</p>
    </div>
</section>

<section class="j-feat">
    <div class="ph" style="background-image:url('{{ asset('images/redesign/filozofia3.jpg') }}')"></div>
    <div class="body">
        <div class="meta">03 — Zmysly</div>
        <h2>Starostlivosť,<br><em>na ktorú sa tešíte.</em></h2>
        <p>Charakteristické vône, príjemné textúry a profesionálne formulácie premieňajú každé použitie na rituál, ktorý si budete chcieť dopriať každý deň.</p>
    </div>
</section>

<section class="pure">
    <div class="pure-head">
        <div class="eyebrow center">04 — Esencia</div>
        <h2 class="h2">Krása začína <em>rovnováhou.</em></h2>
    </div>
    <div class="pure-grid" style="grid-template-columns:1fr">
        <div class="pure-block" style="text-align:center">
            <p class="pure-lead" style="max-width:640px;margin-left:auto;margin-right:auto">PREVIA spája prírodu, vedu a profesionálnu starostlivosť do jedného celku. Výsledkom sú zdravé vlasy, ktoré nielen dobre vyzerajú, ale sa tak aj cítia.</p>
        </div>
    </div>
</section>

<section class="j-list">
    <h3>Naše princípy <small>Na čom staviame</small></h3>
    <div class="howto-grid" style="grid-template-columns: repeat(4, 1fr)">
        <div class="howto-step">
            <div class="n">- 01</div>
            <div class="ti">Rovnováha</div>
            <div class="ds">Rešpektujeme prirodzené potreby vlasov aj pokožky hlavy.</div>
        </div>
        <div class="howto-step">
            <div class="n">- 02</div>
            <div class="ti">Čistota</div>
            <div class="ds">Premyslené receptúry s vysokým podielom prírodných ingrediencií.</div>
        </div>
        <div class="howto-step">
            <div class="n">- 03</div>
            <div class="ti">Účinnosť</div>
            <div class="ds">Profesionálne riešenia s viditeľnými výsledkami.</div>
        </div>
        <div class="howto-step">
            <div class="n">- 04</div>
            <div class="ti">Zážitok</div>
            <div class="ds">Vône a textúry premieňajú starostlivosť na každodenný rituál.</div>
        </div>
    </div>
</section>

@endsection
