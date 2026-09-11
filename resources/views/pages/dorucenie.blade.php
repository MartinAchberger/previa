@extends('layouts.app')

@section('title', 'Doručenie a vrátenie - PREVIA')
@section('description', 'Doručenie na Slovensku od 3,50 €, zadarmo od 60 €. Odstúpenie od zmluvy do 14 dní, vrátenie tovaru a reklamácie PREVIA.')

@section('content')

<section class="shop-head">
    <div class="crumbs">
        <a href="{{ route('home') }}" style="color:inherit;text-decoration:none">PREVIA</a>
        <span class="sep">/</span>
        <span>Doručenie a vrátenie</span>
    </div>
    <h1>Doručenie<br><em>a vrátenie.</em></h1>
    <div class="meta">
        <div class="ds">Vaša nová vlasová rutina, doručená až k vám. Vyberte si doručenie na adresu alebo vyzdvihnutie na výdajnom mieste.</div>
    </div>
</section>

<section class="info-body">
    <aside class="info-nav">
        <a href="#dorucenie">Doručenie na Slovensku</a>
        <a href="#kedy">Kedy dorazí objednávka</a>
        <a href="#odber">Osobný odber</a>
        <a href="#vratenie">Vrátenie tovaru</a>
        <a href="#peniaze">Vrátenie peňazí</a>
        <a href="#reklamacia">Poškodený produkt</a>
    </aside>

    <div class="info-content">
        <div class="info-block" id="dorucenie">
            <div class="line">- 01</div>
            <h2>Doručenie na Slovensku</h2>
            <table class="info-table">
                <thead><tr><th>Spôsob doručenia</th><th>Cena s DPH</th></tr></thead>
                <tbody>
                    <tr><td>Packeta · výdajné miesto</td><td>3,50 €</td></tr>
                    <tr><td>Kuriér na adresu · Packeta, GLS alebo DPD</td><td>4,50 €</td></tr>
                    <tr><td>Osobný odber v Bratislave · pracovné dni 8:00 – 15:30</td><td>Zadarmo</td></tr>
                    <tr><td>Doprava pri nákupe od 60 €</td><td>Zadarmo</td></tr>
                </tbody>
            </table>
            <p class="info-note">Doprava zdarma platí pri hodnote produktov od 60 € po uplatnení zliav, bez započítania dopravy. Dostupné možnosti doručenia sa zobrazia v košíku.</p>
        </div>

        <div class="info-block" id="kedy">
            <div class="line">- 02</div>
            <h2>Kedy vám objednávka dorazí?</h2>
            <p>Objednávku vám doručíme do 1 – 3 pracovných dní, mimo sviatkov. O odoslaní vás budeme informovať e-mailom.</p>
        </div>

        <div class="info-block" id="odber">
            <div class="line">- 03</div>
            <h2>Osobný odber</h2>
            <p>Objednávku si môžete vyzdvihnúť osobne v pracovných dňoch od 8:00 do 15:30 na adrese:</p>
            <address class="info-address">Foxlog Warehouse<br>Stará Vajnorská 11<br>831 04 Bratislava</address>
            <p>Pred vyzdvihnutím, prosím, počkajte na potvrdenie, že je vaša objednávka pripravená na odber.</p>
        </div>

        <div class="info-block" id="vratenie">
            <div class="line">- 04</div>
            <h2>Chcete vrátiť tovar?</h2>
            <p>Pri nákupe ako spotrebiteľ môžete od zmluvy odstúpiť bez uvedenia dôvodu do 14 dní od prevzatia tovaru.</p>
            <p>Odstúpenie oznámte na <a href="mailto:info@previa.sk">info@previa.sk</a> jednoznačným vyhlásením o odstúpení od zmluvy. Pre jednoduchšie vybavenie uveďte číslo objednávky a názvy produktov, ktoré vraciate.</p>
            <p>Tovar následne odošlite najneskôr do 14 dní od oznámenia odstúpenia na adresu:</p>
            <address class="info-address">Foxlog Warehouse<br>Stará Vajnorská 11<br>831 04 Bratislava<br>Slovenská republika</address>
            <p>Produkty bezpečne zabaľte, aby sa počas prepravy nepoškodili. Na jednoduchšiu identifikáciu zásielky priložte číslo objednávky. Náklady na spätné zaslanie pri odstúpení bez uvedenia dôvodu hradíte vy. Zásielku posielajte bez dobierky.</p>
            <h3>Je možné vrátiť aj otvorenú kozmetiku?</h3>
            <p>Pri produktoch dodaných v ochrannom obale, ktoré po jeho porušení nie je vhodné vrátiť z dôvodu ochrany zdravia alebo hygieny, právo na odstúpenie zaniká porušením tohto obalu. Samotné otvorenie prepravnej krabice vráteniu nebráni. Vaše práva pri chybnom tovare tým nie sú dotknuté.</p>
        </div>

        <div class="info-block" id="peniaze">
            <div class="line">- 05</div>
            <h2>Kedy vám vrátime peniaze?</h2>
            <p>Platbu vrátime do 14 dní od doručenia oznámenia o odstúpení, rovnakým spôsobom, aký ste použili pri nákupe. Iný spôsob vrátenia je možný len s vaším výslovným súhlasom a bez dodatočných poplatkov.</p>
            <p>Vrátenie platby môže byť pozastavené do prijatia tovaru alebo preukázania jeho odoslania – podľa toho, čo nastane skôr.</p>
            <p>Pri odstúpení od celej objednávky vrátime aj náklady na pôvodné doručenie do výšky najlacnejšieho bežného spôsobu dopravy, ktorý sme pri nákupe ponúkali.</p>
        </div>

        <div class="info-block" id="reklamacia">
            <div class="line">- 06</div>
            <h2>Prišiel poškodený alebo nesprávny produkt?</h2>
            <p>Napíšte nám na <a href="mailto:info@previa.sk">info@previa.sk</a> a uveďte číslo objednávky a opis problému. Pre rýchlejšie vybavenie priložte fotografie produktu a balenia.</p>
            <p>Následne vás bude kontaktovať naša zákaznícka podpora s informáciami o ďalšom postupe. Náklady oprávnenej reklamácie znášame my.</p>
            <p class="info-note">Podrobné podmienky nájdete v obchodných a reklamačných podmienkach. Zákonné právo na odstúpenie do 14 dní sa vzťahuje na spotrebiteľské nákupy.</p>
        </div>
    </div>
</section>

@endsection
