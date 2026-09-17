@extends('layouts.app')

@section('title', 'Časté otázky - PREVIA')
@section('description', 'Odpovede na najčastejšie otázky o vlasovej kozmetike PREVIA: výber produktov, ingrediencie, domáce použitie, farbené vlasy.')

@section('content')

@php
    $faq = [
        ['Čím je PREVIA výnimočná?', 'PREVIA spája profesionálnu účinnosť s vybranými prírodnými ingredienciami, príjemnými textúrami a premyslenými vôňami. Každý rad cieli na konkrétne potreby vlasov a premieňa každodennú starostlivosť na beauty rituál, na ktorý sa budete tešiť.'],
        ['Ako zistím, ktoré produkty sú pre mňa vhodné?', 'Vyplňte našu online diagnostiku. Na základe vašich odpovedí vám odporučíme produkty podľa potrieb vlasov a pokožky hlavy – aby ste si vybrali starostlivosť, ktorá má pre vás zmysel.', 'quiz'],
        ['Odkiaľ pochádza PREVIA?', 'PREVIA je talianska značka profesionálnej vlasovej kozmetiky. Spája kadernícke know-how s láskou k prírode a talianskym citom pre krásu, vône a pôžitok zo starostlivosti.'],
        ['Ktoré ingrediencie robia PREVIA výnimočnou?', 'Vo vybraných produktoch nájdete organický extrakt z bielej hľuzovky, ľanové semienka či marhuľové extrakty. Rastlinné komplexy sú súčasťou cielenej starostlivosti o regeneráciu, hebkosť, lesk alebo ochranu farby.'],
        ['Je PREVIA vhodná aj na domáce použitie?', 'Áno. Domáca starostlivosť PREVIA vám pomôže starať sa o vlasy aj medzi návštevami salónu. Šampóny, kondicionéry, masky a styling jednoducho zaradíte do svojej rutiny; profesionálne farby a zosvetľovače patria do rúk kaderníka.'],
        ['Stačí mi šampón alebo potrebujem aj kondicionér?', 'Šampón čistí, kondicionér dopĺňa starostlivosť o dĺžky, uľahčuje rozčesávanie a dodáva hebkosť. Spolu tvoria základ rutiny, ktorú môžete podľa potrieb doplniť maskou či bezoplachovou starostlivosťou.'],
        ['Môžem kombinovať produkty z rôznych radov?', 'Áno – vaša pokožka hlavy a končeky môžu mať rozdielne potreby. Šampón vyberajte podľa pokožky a vlasov pri korienkoch, kondicionér či masku podľa stavu dĺžok. S vhodnou kombináciou vám pomôže naša diagnostika.', 'quiz'],
        ['Je PREVIA vhodná aj na farbené či zosvetľované vlasy?', 'Áno. V ponuke nájdete starostlivosť o farbu a lesk, produkty na neutralizáciu neželaných teplých odleskov aj regeneráciu namáhaných vlasov. Svoju rutinu tak môžete prispôsobiť tomu, čo vaše vlasy po farbení potrebujú.'],
    ];
@endphp

<section class="shop-head shop-head--doc">
    <div class="crumbs">
        <a href="{{ route('home') }}" style="color:inherit;text-decoration:none">PREVIA</a>
        <span class="sep">/</span>
        <span>FAQ</span>
    </div>
    <h1>Časté<br><em>otázky.</em></h1>
    <div class="meta">
        <div class="ds">Všetko, čo vás o PREVIA najčastejšie zaujíma. Ak odpoveď nenájdete, <a href="{{ route('contact.show') }}" style="color:inherit">napíšte nám</a>.</div>
    </div>
</section>

<section class="info-body info-body--single">
    <div class="info-content">
        <div class="faq-list">
            @foreach($faq as $i => $item)
                <details class="faq-item" @if($i === 0) open @endif>
                    <summary>
                        <span class="faq-n">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="faq-q">{{ $item[0] }}</span>
                        <span class="faq-chev" aria-hidden="true"></span>
                    </summary>
                    <div class="faq-a">
                        <p>{{ $item[1] }}</p>
                        @if(($item[2] ?? null) === 'quiz')
                            <a href="{{ route('quiz.show') }}" class="btn btn-line" style="margin-top:6px">Spustiť diagnostiku →</a>
                        @endif
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</section>

@endsection
