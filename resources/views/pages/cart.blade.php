@extends('layouts.app')

@section('title', 'Košík - PREVIA')

@section('content')

<section class="shop-head">
    <div class="crumbs">
        <a href="{{ route('home') }}" style="color:inherit;text-decoration:none">PREVIA</a>
        <span class="sep">/</span>
        <span>Košík</span>
    </div>
    <h1>Tvoj <em>košík.</em></h1>
    <div class="meta">
        <div class="ds">Skontroluj objednávku, prejdi na pokladňu a my ju spracujeme. Doprava zadarmo od €60, ku každej objednávke prikladáme vzorky.</div>
    </div>
</section>

<section class="cart-page">
    <div id="cart-page-empty" class="cart-empty" style="display:none">
        <p>Košík je prázdny.</p>
        <a href="{{ route('shop.index') }}" class="btn">Prejsť do eshopu</a>
    </div>
    <div id="cart-page-content" class="cart-content" style="display:none">
        <div class="cart-list" data-cart-list></div>
        <aside class="cart-summary">
            <h3>Súhrn</h3>
            <div class="cs-row"><span>Medzisúčet</span><strong data-cart-sub>€0,00</strong></div>
            <div class="cs-row" style="font-size:12px;color:var(--mute)"><span>z toho DPH 23 %</span><span data-cart-vat>€0,00</span></div>
            <div class="cs-row"><span>Doprava</span><strong data-cart-ship>€0,00</strong></div>
            <div class="cs-row cs-row--big"><span>Celkom</span><strong data-cart-tot>€0,00</strong></div>
            <a href="{{ route('checkout.show') }}" class="btn cs-cta">Pokračovať na pokladňu →</a>
            <p class="cs-note">Platba dobierkou alebo kartou online. Doprava zadarmo od €60.</p>
        </aside>
    </div>
</section>

@push('scripts')
<script>
(function () {
    function fmt(n) { return '€' + n.toFixed(2).replace('.', ','); }
    // Cart items originate from localStorage / admin-set product data — never trust
    // them as markup. Escape all text and validate the swatch colour before use.
    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    function safeColor(s) {
        return /^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(String(s || '')) ? s : '';
    }

    function render() {
        const c = window.PhCart;
        if (!c) return;
        const empty = document.getElementById('cart-page-empty');
        const wrap  = document.getElementById('cart-page-content');
        if (c.items.length === 0) {
            empty.style.display = 'block';
            wrap.style.display = 'none';
            return;
        }
        empty.style.display = 'none';
        wrap.style.display = 'grid';

        const list = document.querySelector('[data-cart-list]');
        list.innerHTML = c.items.map(it => {
            const col = safeColor(it.swatch);
            const sw = col
                ? `<span class="cl-swatch" style="background:${col}" aria-hidden="true"></span>`
                : '';
            return `
            <div class="cart-line" data-id="${esc(it.id)}">
                <div class="cl-info">
                    <div class="cl-name">${sw}<span>${esc(it.name)}</span></div>
                    <div class="cl-meta">${esc(it.line || '')} · ${esc(it.volume || '')}</div>
                </div>
                <div class="cl-qty">
                    <button data-act="dec" aria-label="Znížiť počet">−</button>
                    <span>${esc(it.qty)}</span>
                    <button data-act="inc" aria-label="Zvýšiť počet">+</button>
                </div>
                <div class="cl-pr">${fmt(it.qty * it.price)}</div>
                <button class="cl-rm" data-act="rm" aria-label="Odstrániť položku">×</button>
            </div>`;
        }).join('');

        const sub = c.subtotal();
        const vat = sub - sub / 1.23;
        const ship = sub >= 60 ? 0 : 4.90;
        const tot = sub + ship;
        document.querySelector('[data-cart-sub]').textContent = fmt(sub);
        document.querySelector('[data-cart-vat]').textContent = fmt(vat);
        document.querySelector('[data-cart-ship]').textContent = ship === 0 ? 'zadarmo' : fmt(ship);
        document.querySelector('[data-cart-tot]').textContent = fmt(tot);
    }

    document.addEventListener('cart:change', render);
    document.addEventListener('DOMContentLoaded', render);

    document.addEventListener('click', (e) => {
        const line = e.target.closest('.cart-line');
        if (!line) return;
        const id = line.dataset.id;
        const act = e.target.dataset.act;
        const c = window.PhCart;
        if (!c) return;
        if (act === 'inc') c.update(id, (c.find(id)?.qty || 1) + 1);
        else if (act === 'dec') c.update(id, Math.max(1, (c.find(id)?.qty || 1) - 1));
        else if (act === 'rm')  c.remove(id);
    });
})();
</script>
@endpush

@endsection
