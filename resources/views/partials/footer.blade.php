<footer class="ft">
    <div class="ft-top">
        <div>
            <div class="b">
                <span class="ft-word">Previa</span>
                <span class="ft-sub">Italian Haircare · Distribúcia SK</span>
            </div>
            <p class="ft-blurb">Profesionálna vlasová starostlivosť inšpirovaná prírodou. Objavte kolekcie PREVIA vytvorené pre zdravú pokožku hlavy a prirodzene krásne vlasy.</p>
        </div>
        <div>
            <h4>Eshop</h4>
            <ul>
                <li><a href="{{ route('shop.index', ['line' => 'reconstruct']) }}">Reconstruct</a></li>
                <li><a href="{{ route('shop.index', ['line' => 'keeping-after-color']) }}">Keeping After Color</a></li>
                <li><a href="{{ route('shop.index', ['line' => 'curl-friends']) }}">Curl Friends</a></li>
                <li><a href="{{ route('shop.index', ['line' => 'blonde']) }}">Blonde</a></li>
                <li><a href="{{ route('shop.index', ['line' => 'styling-and-basics']) }}">Styling and Basics</a></li>
                <li><a href="{{ route('shop.index') }}">Všetky produkty</a></li>
            </ul>
        </div>
        <div>
            <h4>O značke</h4>
            <ul>
                <li><a href="{{ route('philosophy.show') }}">Filozofia</a></li>
                <li><a href="{{ route('quiz.show') }}">Diagnostika vlasov</a></li>
                <li>Vegan &amp; cruelty-free</li>
                <li>Eco-friendly obaly</li>
            </ul>
        </div>
        <div>
            <h4>Pomoc</h4>
            <ul>
                <li>Doprava &amp; vrátenie</li>
                <li>FAQ</li>
                <li>Kontakt</li>
            </ul>
        </div>
        <div>
            <h4>Pre salóny</h4>
            <ul>
                <li><a href="{{ route('b2b.register') }}">Získať prístup pre salón</a></li>
                <li><a href="{{ route('b2b.login') }}">Prihlásenie</a></li>
                <li>Veľkoobchodný cenník</li>
                <li>Edukácia</li>
            </ul>
        </div>
    </div>
    <div class="ft-bot">
        <span>© {{ date('Y') }} PREVIA Slovensko</span>
        <div class="links">
            <span>Obchodné podmienky</span>
            <span>GDPR</span>
            <span>Cookies</span>
        </div>
    </div>
</footer>
