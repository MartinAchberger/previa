@extends('layouts.app')

@section('title', 'Zásady spracovania osobných údajov - PREVIA')
@section('description', 'Ako spoločnosť Volkov s.r.o. spracúva osobné údaje zákazníkov internetového obchodu previa.sk, na aké účely, komu ich odovzdáva a aké máte práva.')

@section('content')

<section class="shop-head shop-head--doc">
    <div class="crumbs">
        <a href="{{ route('home') }}" style="color:inherit;text-decoration:none">PREVIA</a>
        <span class="sep">/</span>
        <span>Osobné údaje</span>
    </div>
    <h1>Zásady spracovania<br><em>osobných údajov.</em></h1>
    <div class="meta">
        <div class="ds">Informácie pre zákazníkov a návštevníkov internetového obchodu previa.sk podľa nariadenia GDPR a zákona č. 18/2018 Z. z. Platné od 17. 9. 2026.</div>
    </div>
</section>

<section class="info-body info-body--single">
    <div class="info-content">
<div class="info-block">
            <h2>1. Kto je prevádzkovateľ</h2>
        <p>Prevádzkovateľom osobných údajov je spoločnosť, ktorá prevádzkuje internetový obchod previa.sk:</p>
        <address class="info-address">
            Volkov s.r.o.<br>
            Floriánska 8, 811 02 Bratislava, Slovenská republika<br>
            IČO: 52 409 601 · DIČ: 2121028734 · IČ DPH: SK2121028734<br>
            zapísaná v Obchodnom registri Mestského súdu Bratislava III, oddiel: Sro, vložka č. 138113/B<br>
            E-mail: <a href="mailto:info@previa.sk">info@previa.sk</a> · Telefón: <a href="tel:+421903430149">+421 903 430 149</a>
        </address>
        <p>So všetkými otázkami a žiadosťami týkajúcimi sa osobných údajov sa na nás obráťte na uvedenom e-maile. Zodpovednú osobu sme nemenovali, pretože nám túto povinnosť zákon neukladá.</p>
        </div>


        <div class="info-block">
            <h2>2. Aké údaje spracúvame a prečo</h2>
        <p><strong>Objednávka a jej vybavenie.</strong> Pri nákupe spracúvame meno a priezvisko, fakturačnú a doručovaciu adresu, e-mail, telefónne číslo, údaje o objednanom tovare a platbe; pri nákupe na firmu aj obchodné meno, IČO, DIČ a IČ DPH. Právnym základom je plnenie kúpnej zmluvy a plnenie zákonných povinností (účtovné a daňové predpisy). Bez týchto údajov nevieme objednávku prijať a doručiť.</p>
        <p><strong>Účet pre salóny.</strong> Pri registrácii kaderníckeho salónu spracúvame názov salónu, meno kontaktnej osoby, e-mail, telefón, adresu, IČO a IČ DPH a údaje o objednávkach. Právnym základom je plnenie zmluvy, resp. opatrenia pred jej uzavretím (schválenie registrácie).</p>
        <p><strong>Komunikácia so zákazníkom.</strong> Ak nám napíšete alebo zavoláte, spracúvame vaše kontaktné údaje a obsah správy, aby sme vybavili vašu otázku, reklamáciu alebo odstúpenie od zmluvy. Právnym základom je plnenie zmluvy alebo náš oprávnený záujem odpovedať na dopyty.</p>
        <p><strong>Diagnostika vlasov.</strong> Online diagnostika na našom webe odporúča produkty priamo v prehliadači. Vaše odpovede neukladáme ani nespájame s vašou osobou.</p>
        <p><strong>Cookies a návštevnosť.</strong> Nevyhnutné cookies používame na fungovanie košíka a pokladne. Štatistické alebo marketingové cookies používame len s vaším súhlasom udeleným v cookie lište, ktorý môžete kedykoľvek zmeniť na stránke <a href="{{ route('cookies.show') }}">Cookies</a>.</p>
        <p><strong>Obchodné oznámenia.</strong> Novinky a ponuky vám e-mailom posielame len vtedy, ak sa na ich odber prihlásite, a to na základe vášho súhlasu. Odhlásiť sa môžete kedykoľvek odkazom v každom e-maile alebo na našom e-maile.</p>
        </div>


        <div class="info-block">
            <h2>3. Komu údaje odovzdávame</h2>
        <p>Údaje odovzdávame len partnerom, ktorých potrebujeme na vybavenie vašej objednávky, a len v nevyhnutnom rozsahu:</p>
        <ul class="info-list">
            <li>logistický sklad Foxlog (balenie a expedícia objednávok, osobný odber),</li>
            <li>dopravcovia Packeta, GLS a DPD (doručenie zásielky, kontaktné údaje pre kuriéra),</li>
            <li>Stripe (spracovanie platby kartou; údaje o karte zadávate priamo Stripe, my ich nevidíme),</li>
            <li>SuperFaktúra (vystavovanie faktúr a účtovníctvo),</li>
            <li>poskytovateľ webhostingu a e-mailových služieb, na ktorých beží náš eshop,</li>
            <li>Cookiebot (správa súhlasu s cookies),</li>
            <li>účtovná kancelária, právni poradcovia a orgány verejnej moci, ak nám to ukladá zákon.</li>
        </ul>
        <p>Vaše údaje nepredávame a neposkytujeme tretím stranám na ich vlastné marketingové účely. Ak niektorý partner spracúva údaje mimo Európskeho hospodárskeho priestoru (napríklad Stripe), deje sa tak na základe štandardných zmluvných doložiek alebo rozhodnutia Európskej komisie o primeranosti.</p>
        </div>


        <div class="info-block">
            <h2>4. Ako dlho údaje uchovávame</h2>
        <ul class="info-list">
            <li>údaje o objednávkach a faktúry: 10 rokov od konca roka, v ktorom bola objednávka vybavená (účtovné a daňové predpisy),</li>
            <li>údaje potrebné na vybavenie reklamácií a odstúpení: po dobu trvania záruky a premlčacích lehôt,</li>
            <li>účet salónu: po dobu trvania spolupráce a 3 roky po jej skončení,</li>
            <li>komunikácia: 3 roky od jej ukončenia,</li>
            <li>súhlas s obchodnými oznámeniami: do jeho odvolania,</li>
            <li>cookies: podľa doby uvedenej v zozname na stránke Cookies.</li>
        </ul>
        <p>Po uplynutí doby uchovávania údaje vymažeme alebo anonymizujeme.</p>
        </div>


        <div class="info-block">
            <h2>5. Vaše práva</h2>
        <p>V súvislosti so spracúvaním osobných údajov máte právo:</p>
        <ul class="info-list">
            <li>na prístup k údajom, ktoré o vás spracúvame, a na ich kópiu,</li>
            <li>na opravu nesprávnych alebo neúplných údajov,</li>
            <li>na vymazanie údajov, ak už nie sú potrebné alebo ich spracúvame neoprávnene,</li>
            <li>na obmedzenie spracúvania,</li>
            <li>na prenosnosť údajov, ktoré ste nám poskytli, v strojovo čitateľnom formáte,</li>
            <li>namietať proti spracúvaniu založenému na oprávnenom záujme, vrátane priameho marketingu,</li>
            <li>kedykoľvek odvolať súhlas, ak je spracúvanie založené na súhlase; odvolanie nemá vplyv na zákonnosť spracúvania pred ním.</li>
        </ul>
        <p>Žiadosť pošlite na <a href="mailto:info@previa.sk">info@previa.sk</a>. Odpovieme najneskôr do jedného mesiaca; pri zložitých žiadostiach môžeme lehotu predĺžiť o ďalšie dva mesiace, o čom vás informujeme. Ak máte za to, že vaše údaje spracúvame v rozpore s právnymi predpismi, môžete podať sťažnosť Úradu na ochranu osobných údajov Slovenskej republiky, Hraničná 12, 820 07 Bratislava, <a href="https://dataprotection.gov.sk" target="_blank" rel="noopener">dataprotection.gov.sk</a>.</p>
        </div>


        <div class="info-block">
            <h2>6. Zabezpečenie a záverečné ustanovenia</h2>
        <p>Údaje chránime technickými a organizačnými opatreniami: šifrovaným pripojením (HTTPS), obmedzeným prístupom len pre oprávnené osoby, zabezpečeným hostingom a zmluvami so spracovateľmi. Neprijímame rozhodnutia založené výlučne na automatizovanom spracúvaní vrátane profilovania, ktoré by mali pre vás právne účinky.</p>
        <p>Tieto zásady môžeme aktualizovať, napríklad pri zmene partnerov alebo právnych predpisov. Aktuálne znenie je vždy zverejnené na tejto stránke.</p>
        </div>
    </div>
</section>

@endsection
