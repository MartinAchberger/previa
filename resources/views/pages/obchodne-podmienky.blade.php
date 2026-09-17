@extends('layouts.app')

@section('title', 'Obchodné podmienky - PREVIA')
@section('description', 'Všeobecné obchodné a reklamačné podmienky internetového obchodu previa.sk prevádzkovaného spoločnosťou Volkov s.r.o.')

@section('content')

<section class="shop-head">
    <div class="crumbs">
        <a href="{{ route('home') }}" style="color:inherit;text-decoration:none">PREVIA</a>
        <span class="sep">/</span>
        <span>Obchodné podmienky</span>
    </div>
    <h1>Obchodné<br><em>podmienky.</em></h1>
    <div class="meta">
        <div class="ds">Všeobecné obchodné a reklamačné podmienky internetového obchodu previa.sk. Platné od 17. 9. 2026.</div>
    </div>
</section>

<section class="info-body info-body--single">
    <div class="info-content">
<div class="info-block">
            <h2>1. Predávajúci a úvodné ustanovenia</h2>
        <p>Tieto všeobecné obchodné a reklamačné podmienky (ďalej len „podmienky“) upravujú práva a povinnosti predávajúceho a kupujúceho pri kúpe tovaru prostredníctvom internetového obchodu na adrese previa.sk (ďalej len „eshop“).</p>
        <p><strong>Predávajúci:</strong></p>
        <address class="info-address">
            Volkov s.r.o.<br>
            Floriánska 8, 811 02 Bratislava, Slovenská republika<br>
            IČO: 52 409 601 · DIČ: 2121028734 · IČ DPH: SK2121028734<br>
            zapísaná v Obchodnom registri Mestského súdu Bratislava III, oddiel: Sro, vložka č. 138113/B<br>
            E-mail: <a href="mailto:info@previa.sk">info@previa.sk</a> · Telefón: <a href="tel:+421903430149">+421 903 430 149</a>
        </address>
        <p><strong>Orgán dozoru:</strong> Slovenská obchodná inšpekcia (SOI), Inšpektorát SOI pre Bratislavský kraj, Bajkalská 21/A, P. O. BOX č. 5, 820 07 Bratislava, odbor výkonu dohľadu, ba@soi.sk, tel. 02/58 27 21 72, <a href="https://www.soi.sk" target="_blank" rel="noopener">www.soi.sk</a>.</p>
        <p>Kupujúcim je spotrebiteľ alebo podnikateľ. Spotrebiteľom je fyzická osoba, ktorá pri uzatváraní zmluvy nekoná v rámci svojej podnikateľskej činnosti alebo povolania. Ustanovenia týchto podmienok o odstúpení od zmluvy a o reklamácii, ktoré vyplývajú z predpisov na ochranu spotrebiteľa, sa vzťahujú len na spotrebiteľa.</p>
        <p>Podmienky v znení platnom v deň odoslania objednávky sú neoddeliteľnou súčasťou kúpnej zmluvy. Zmluva sa uzatvára v slovenskom jazyku a predávajúci ju uchováva v elektronickej podobe; kupujúcemu je dostupná na požiadanie.</p>
        </div>


        <div class="info-block">
            <h2>2. Objednávka a uzavretie zmluvy</h2>
        <p>Kupujúci objednáva tovar vyplnením a odoslaním objednávkového formulára v eshope. Odoslaná objednávka je návrhom na uzavretie kúpnej zmluvy. Pred odoslaním objednávky má kupujúci možnosť skontrolovať a zmeniť jej obsah a je informovaný o hlavných vlastnostiach tovaru, celkovej cene vrátane DPH, nákladoch na dopravu, spôsobe platby a dodania a o práve odstúpiť od zmluvy.</p>
        <p>Po odoslaní objednávky predávajúci zašle na e-mailovú adresu kupujúceho potvrdenie o prijatí objednávky s jej zhrnutím. Kúpna zmluva je uzavretá doručením tohto potvrdenia. Pri platbe kartou je zmluva uzavretá okamihom úspešného zaplatenia.</p>
        <p>Ak predávajúci nemôže objednávku splniť (napríklad tovar bol vypredaný alebo sa prestal vyrábať), bezodkladne o tom kupujúceho informuje a ponúkne mu náhradné plnenie alebo zrušenie objednávky. Ak kupujúci už zaplatil, predávajúci mu vráti všetky prijaté platby do 14 dní.</p>
        <p>Predávajúci môže objednávku odmietnuť, ak kupujúci v minulosti neprevzal alebo nezaplatil objednaný tovar, alebo ak sú údaje v objednávke zjavne nesprávne.</p>
        </div>


        <div class="info-block">
            <h2>3. Ceny a platba</h2>
        <p>Ceny tovaru v eshope sú uvedené v eurách vrátane DPH. Nezahŕňajú náklady na dopravu, ktoré sú uvedené samostatne v košíku a v objednávke. Akcie a zľavy platia do odvolania alebo do vypredania zásob. Predávajúci môže ceny meniť; zmena sa netýka už odoslaných objednávok.</p>
        <p>Kupujúci môže zaplatiť:</p>
        <ul class="info-list">
            <li>platobnou kartou online prostredníctvom platobnej brány Stripe,</li>
            <li>dobierkou pri prevzatí tovaru,</li>
            <li>bankovým prevodom na základe faktúry, ak ide o registrovaný salón s povoleným nákupom na faktúru.</li>
        </ul>
        <p>Pri platbe prevodom sa za deň zaplatenia považuje deň pripísania celej sumy na účet predávajúceho. Daňový doklad (faktúru) zasiela predávajúci kupujúcemu elektronicky na e-mailovú adresu uvedenú v objednávke.</p>
        </div>


        <div class="info-block">
            <h2>4. Dodanie tovaru</h2>
        <p>Tovar doručujeme v rámci Slovenskej republiky. Spôsoby doručenia a ich cena:</p>
        <table class="info-table">
            <thead><tr><th>Spôsob doručenia</th><th>Cena s DPH</th></tr></thead>
            <tbody>
                <tr><td>Packeta - výdajné miesto</td><td>3,50 €</td></tr>
                <tr><td>Kuriér na adresu - Packeta, GLS alebo DPD</td><td>4,50 €</td></tr>
                <tr><td>Osobný odber v Bratislave (Foxlog Warehouse, Stará Vajnorská 11, pracovné dni 8:00 – 15:30)</td><td>Zadarmo</td></tr>
                <tr><td>Doprava pri nákupe od 60 €</td><td>Zadarmo</td></tr>
            </tbody>
        </table>
        <p>Objednaný tovar odosielame spravidla do 1 – 3 pracovných dní od uzavretia zmluvy, pri platbe kartou od zaplatenia. Najneskôr dodáme tovar do 30 dní; ak sa tak nestane ani v dodatočnej primeranej lehote, ktorú kupujúci určí, môže kupujúci od zmluvy odstúpiť. O odoslaní kupujúceho informujeme e-mailom. Pri osobnom odbere kupujúci počká na potvrdenie, že je objednávka pripravená.</p>
        <p>Kupujúci je povinný tovar prevziať. Odporúčame pri prevzatí skontrolovať neporušenosť obalu; ak je zásielka zjavne poškodená, kupujúci ju nemusí prevziať a spíše s dopravcom záznam o poškodení. Neskoršie reklamácie mechanického poškodenia to nevylučuje.</p>
        <p>Vlastnícke právo k tovaru prechádza na kupujúceho prevzatím tovaru a úplným zaplatením kúpnej ceny. Nebezpečenstvo škody na tovare prechádza na kupujúceho prevzatím tovaru.</p>
        </div>


        <div class="info-block">
            <h2>5. Odstúpenie od zmluvy</h2>
        <p>Spotrebiteľ má právo odstúpiť od kúpnej zmluvy bez uvedenia dôvodu do 14 dní odo dňa prevzatia tovaru. Ak sa tovar z jednej objednávky dodáva po častiach, lehota plynie od prevzatia poslednej časti. Odstúpiť možno aj pred prevzatím tovaru.</p>
        <p>Odstúpenie spotrebiteľ oznámi predávajúcemu jednoznačným vyhlásením, najjednoduchšie e-mailom na <a href="mailto:info@previa.sk">info@previa.sk</a>, prípadne pomocou formulára v prílohe týchto podmienok. Lehota je zachovaná, ak bolo oznámenie odoslané v jej posledný deň.</p>
        <p>Spotrebiteľ je povinný zaslať tovar späť najneskôr do 14 dní od odstúpenia na adresu: <strong>Foxlog Warehouse, Stará Vajnorská 11, 831 04 Bratislava</strong>. Zásielky na dobierku nepreberáme. Náklady na vrátenie tovaru znáša spotrebiteľ. Odporúčame tovar zabaliť tak, aby sa pri preprave nepoškodil, a priložiť číslo objednávky.</p>
        <p>Predávajúci vráti spotrebiteľovi všetky platby vrátane nákladov na doručenie (do výšky najlacnejšieho ponúkaného bežného spôsobu) do 14 dní od doručenia oznámenia o odstúpení, rovnakým spôsobom, aký spotrebiteľ použil pri platbe, ak sa nedohodnú inak. Predávajúci nie je povinný vrátiť platby skôr, ako mu je tovar doručený alebo kým spotrebiteľ nepreukáže jeho odoslanie.</p>
        <p>Spotrebiteľ môže tovar v lehote na odstúpenie vyskúšať v rozsahu potrebnom na zistenie jeho povahy a vlastností, tak ako v kamennej predajni. Zodpovedá za zníženie hodnoty tovaru, ktoré vzniklo zaobchádzaním nad tento rámec.</p>
        <p><strong>Kedy nemožno odstúpiť:</strong> spotrebiteľ nemôže odstúpiť od zmluvy pri tovare uzavretom v ochrannom obale, ktorý nie je vhodné vrátiť z dôvodu ochrany zdravia alebo z hygienických dôvodov, ak bol tento obal po dodaní porušený. Týka sa to najmä kozmetických prípravkov s porušenou ochrannou fóliou, pečaťou alebo otvoreným uzáverom. Otvorenie prepravnej krabice vráteniu nebráni. Odstúpiť nemožno ani pri tovare zhotovenom podľa osobitných požiadaviek kupujúceho.</p>
        <p>Ustanovenia tohto článku sa nevzťahujú na kupujúceho, ktorý nie je spotrebiteľom.</p>
        </div>


        <div class="info-block">
            <h2>6. Zodpovednosť za vady a reklamácie</h2>
        <p>Predávajúci zodpovedá za vady, ktoré má tovar pri prevzatí, a za vady, ktoré sa vyskytnú do dvoch rokov od prevzatia, v rozsahu podľa Občianskeho zákonníka. Pri kozmetických prípravkoch s vyznačeným dátumom minimálnej trvanlivosti alebo dobou použiteľnosti po otvorení zodpovedá predávajúci za vady, ktoré sa vyskytnú do uplynutia tejto doby.</p>
        <p>Predávajúci nezodpovedá za vady spôsobené bežným opotrebením, nesprávnym skladovaním alebo použitím v rozpore s návodom, mechanickým poškodením po prevzatí, ani za vady, na ktoré bol kupujúci pri kúpe upozornený.</p>
        <p><strong>Ako reklamovať:</strong> reklamáciu uplatní kupujúci e-mailom na <a href="mailto:info@previa.sk">info@previa.sk</a>, kde uvedie číslo objednávky, označenie tovaru, opis vady a aké právo si uplatňuje. Pre rýchlejšie vybavenie odporúčame priložiť fotografie tovaru a balenia. Reklamovaný tovar zašle kupujúci po dohode s predávajúcim na adresu Foxlog Warehouse, Stará Vajnorská 11, 831 04 Bratislava. Zásielky na dobierku nepreberáme.</p>
        <p>Predávajúci vydá kupujúcemu potvrdenie o uplatnení reklamácie bezodkladne, najneskôr spolu s dokladom o jej vybavení. Reklamáciu vybaví najneskôr do 30 dní od jej uplatnenia; po márnom uplynutí tejto lehoty má spotrebiteľ právo odstúpiť od zmluvy alebo požadovať výmenu tovaru.</p>
        <p><strong>Práva kupujúceho z vadného plnenia:</strong> kupujúci má právo na odstránenie vady opravou alebo výmenou. Ak predávajúci vadu neodstráni, odmietne ju odstrániť, vada sa vyskytne opakovane, alebo ide o vadu, ktorú nemožno odstrániť, má kupujúci právo na primeranú zľavu z kúpnej ceny alebo na odstúpenie od zmluvy. Náklady oprávnenej reklamácie vrátane nákladov na doručenie tovaru znáša predávajúci.</p>
        <p>Ak kupujúci, ktorý nie je spotrebiteľom, uplatní reklamáciu, riadi sa jej vybavenie Obchodným zákonníkom a záručná doba je 12 mesiacov od prevzatia tovaru.</p>
        </div>


        <div class="info-block">
            <h2>7. Alternatívne riešenie sporov</h2>
        <p>Ak spotrebiteľ nie je spokojný so spôsobom vybavenia reklamácie alebo sa domnieva, že predávajúci porušil jeho práva, môže sa obrátiť na predávajúceho so žiadosťou o nápravu. Ak predávajúci žiadosť zamietne alebo na ňu neodpovie do 30 dní, má spotrebiteľ právo podať návrh na začatie alternatívneho riešenia sporu podľa zákona č. 391/2015 Z. z. Príslušným subjektom je Slovenská obchodná inšpekcia (<a href="https://www.soi.sk" target="_blank" rel="noopener">www.soi.sk</a>) alebo iný subjekt zapísaný v zozname vedenom Ministerstvom hospodárstva SR. Spotrebiteľ môže využiť aj európsku platformu riešenia sporov online na <a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener">ec.europa.eu/consumers/odr</a>.</p>
        </div>


        <div class="info-block">
            <h2>8. Ochrana osobných údajov</h2>
        <p>Prevádzkovateľom osobných údajov je Volkov s.r.o. Osobné údaje kupujúceho (meno, adresa, e-mail, telefón, pri podnikateľoch aj obchodné meno, IČO, DIČ a IČ DPH) spracúvame na účel uzavretia a plnenia kúpnej zmluvy, vystavenia daňových dokladov a plnenia zákonných povinností, a to bez súhlasu kupujúceho na základe zmluvy a zákona.</p>
        <p>Údaje odovzdávame len v nevyhnutnom rozsahu spracovateľom a príjemcom, ktorí zabezpečujú plnenie zmluvy: logistickému partnerovi Foxlog (sklad a expedícia), dopravcom (Packeta, GLS, DPD), poskytovateľovi platobnej brány Stripe a fakturačnej službe SuperFaktúra. Údaje uchovávame po dobu trvania zmluvného vzťahu a následne po dobu vyžadovanú účtovnými a daňovými predpismi.</p>
        <p>Kupujúci má právo na prístup k údajom, ich opravu, vymazanie, obmedzenie spracúvania, prenosnosť a právo namietať. Žiadosti vybavujeme na <a href="mailto:info@previa.sk">info@previa.sk</a>. Kupujúci má právo podať sťažnosť Úradu na ochranu osobných údajov SR. Podrobnosti sú uvedené v <a href="{{ route('privacy.show') }}">Zásadách spracovania osobných údajov</a>.</p>
        </div>


        <div class="info-block">
            <h2>9. Nákup pre salóny</h2>
        <p>Registrovaným kaderníckym salónom a profesionálom (kupujúci - podnikateľ) sprístupňuje predávajúci po schválení registrácie profesionálne produkty a veľkoobchodné ceny. Profesionálne farby, zosvetľovače a salónne ošetrenia sú určené výhradne odborníkom a predávajú sa len takto registrovaným kupujúcim.</p>
        <p>Na nákup podnikateľa sa nevzťahujú ustanovenia o ochrane spotrebiteľa, najmä právo na odstúpenie od zmluvy do 14 dní bez uvedenia dôvodu. Platba na faktúru je možná len po dohode a v lehote splatnosti uvedenej na faktúre; pri omeškaní môže predávajúci ďalšie objednávky na faktúru odmietnuť.</p>
        </div>


        <div class="info-block">
            <h2>10. Záverečné ustanovenia</h2>
        <p>Vzťahy neupravené týmito podmienkami sa riadia právnym poriadkom Slovenskej republiky, najmä Občianskym zákonníkom, zákonom č. 108/2024 Z. z. o ochrane spotrebiteľa, zákonom č. 22/2004 Z. z. o elektronickom obchode a pri podnikateľoch Obchodným zákonníkom.</p>
        <p>Komunikácia medzi predávajúcim a kupujúcim prebieha prevažne e-mailom. Kupujúci odoslaním objednávky potvrdzuje, že sa s týmito podmienkami oboznámil a súhlasí s nimi. Predávajúci môže podmienky meniť; pre kupujúceho platí znenie účinné v deň odoslania objednávky.</p>
        <p>Tieto podmienky nadobúdajú účinnosť 17. 9. 2026.</p>
        </div>


        <div class="info-block">
            <h2>Príloha: formulár na odstúpenie</h2>
        <p>Vyplňte a odošlite len v prípade, že chcete odstúpiť od zmluvy. Stačí poslať e-mailom na <a href="mailto:info@previa.sk">info@previa.sk</a>.</p>
        <address class="info-address">
            Komu: Volkov s.r.o., Floriánska 8, 811 02 Bratislava, info@previa.sk<br><br>
            Týmto oznamujem, že odstupujem od kúpnej zmluvy na tento tovar: ..........<br>
            Číslo objednávky: .......... · Dátum objednania / prevzatia: ..........<br>
            Meno a priezvisko spotrebiteľa: ..........<br>
            Adresa spotrebiteľa: ..........<br>
            Číslo účtu (IBAN) na vrátenie platby: ..........<br>
            Dátum: .......... · Podpis (len ak sa formulár podáva v listinnej podobe): ..........
        </address>
        </div>
    </div>
</section>

@endsection
