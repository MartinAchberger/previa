<div id="cart-drawer" class="cd">
    <div class="cd-bg" data-cart-close></div>
    <aside class="cd-panel">
        <div class="cd-head">
            <div class="cd-title">Košík</div>
            <button class="cd-x" data-cart-close aria-label="Zavrieť">×</button>
        </div>
        <div class="cd-body">
            <div class="cd-list"></div>
        </div>
        <div class="cd-foot">
            <div class="cd-row"><span>Medzisúčet</span><strong class="cd-sub">€0,00</strong></div>
            <div class="cd-row" style="font-size:11px;color:var(--mute)"><span>z toho DPH 23 %</span><span class="cd-vat">€0,00</span></div>
            <div class="cd-row"><span>Doprava</span><strong class="cd-ship">€0,00</strong></div>
            <div class="cd-row cd-row--big"><span>Celkom</span><strong class="cd-tot">€0,00</strong></div>
            <a href="{{ route('checkout.show') }}" class="btn cd-checkout">Pokračovať na pokladňu →</a>
            <a href="{{ route('shop.index') }}" class="cd-link" data-cart-close>Pokračovať v nákupe</a>
            <p class="cd-note">Doprava zadarmo od €60. Vzorky ku každej objednávke.</p>
        </div>
    </aside>
</div>
