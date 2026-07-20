/* PREVIA — client-side cart (localStorage) */
(function () {
    'use strict';

    const KEY = 'ph_cart_v1';

    // Escape text before it goes into innerHTML (cart items come from product
    // data that an admin could set — never trust it as markup).
    const esc = (v) => String(v == null ? '' : v).replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));
    // Only allow a safe CSS colour (hex / rgb / hsl / plain name) for the swatch.
    const safeColor = (v) => /^#[0-9a-fA-F]{3,8}$|^(rgb|hsl)a?\([\d%.,\s/]+\)$|^[a-zA-Z]+$/.test(String(v || '').trim())
        ? String(v).trim() : null;

    const MAX_QTY = 99;
    const clampQty = (n) => {
        n = Math.floor(Number(n));
        if (!Number.isFinite(n) || n < 1) return 1;
        return Math.min(MAX_QTY, n);
    };

    const Cart = {
        items: [],
        load() {
            let raw;
            try { raw = JSON.parse(localStorage.getItem(KEY) || '[]') || []; }
            catch (e) { raw = []; }
            // Coerce stored items into a known-good shape so a corrupted/legacy
            // entry can't produce "€NaN" totals or an over-cap quantity.
            this.items = (Array.isArray(raw) ? raw : [])
                .filter(i => i && i.id != null)
                .map(i => ({
                    ...i,
                    id: String(i.id),
                    price: Number.isFinite(parseFloat(i.price)) ? parseFloat(i.price) : 0,
                    qty: clampQty(i.qty),
                }));
        },
        save() { localStorage.setItem(KEY, JSON.stringify(this.items)); this.render(); this.emit(); },
        emit() { document.dispatchEvent(new CustomEvent('cart:change', { detail: { items: this.items, count: this.count() } })); },
        count() { return this.items.reduce((s, i) => s + i.qty, 0); },
        subtotal() { return this.items.reduce((s, i) => s + i.qty * i.price, 0); },
        find(id) { return this.items.find(i => i.id === id); },
        add(item) {
            const ex = this.find(item.id);
            if (ex) ex.qty = clampQty(ex.qty + (item.qty || 1));
            else this.items.push({ ...item, qty: clampQty(item.qty || 1) });
            this.save();
            this.flash(`${item.name} pridané do košíka`);
        },
        update(id, qty) {
            const it = this.find(id);
            if (!it) return;
            it.qty = clampQty(qty);
            this.save();
        },
        remove(id) {
            this.items = this.items.filter(i => i.id !== id);
            this.save();
        },
        clear() { this.items = []; this.save(); },
        flash(text) {
            let el = document.getElementById('ph-flash');
            if (!el) {
                el = document.createElement('div');
                el.id = 'ph-flash';
                el.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#0b0b0a;color:#fff;padding:14px 20px;font-size:13px;letter-spacing:0.06em;z-index:1000;opacity:0;transition:opacity .2s;font-family:Inter,sans-serif;';
                document.body.appendChild(el);
            }
            el.textContent = text;
            el.style.opacity = '1';
            clearTimeout(this._ft);
            this._ft = setTimeout(() => el.style.opacity = '0', 2200);
        },
        render() {
            const c = this.count();
            // Update header counter
            document.querySelectorAll('[data-cart-count]').forEach(el => {
                el.textContent = `Košík · ${c}`;
            });
            // Update drawer if open
            this.renderDrawer();
        },
        renderDrawer() {
            const drawer = document.getElementById('cart-drawer');
            if (!drawer) return;
            const list = drawer.querySelector('.cd-list');
            const subEl = drawer.querySelector('.cd-sub');
            const vatEl = drawer.querySelector('.cd-vat');
            const shipEl = drawer.querySelector('.cd-ship');
            const totEl = drawer.querySelector('.cd-tot');
            if (!list) return;

            if (this.items.length === 0) {
                list.innerHTML = '<div class="cd-empty">Košík je prázdny.<br><small>Pridajte si produkty z eshopu.</small></div>';
                if (subEl) subEl.textContent = '€0,00';
                if (vatEl) vatEl.textContent = '€0,00';
                if (shipEl) shipEl.textContent = '€0,00';
                if (totEl) totEl.textContent = '€0,00';
                return;
            }

            list.innerHTML = this.items.map(it => {
                const col = safeColor(it.swatch);
                const swatch = col
                    ? `<span class="cd-swatch" style="background:${col}" aria-hidden="true"></span>`
                    : '';
                return `
                <div class="cd-it" data-id="${esc(it.id)}">
                    <div class="cd-info">
                        <div class="cd-name">${swatch}<span>${esc(it.name)}</span></div>
                        <div class="cd-sub-line">${esc(it.line || '')} · ${esc(it.volume || '')}</div>
                    </div>
                    <div class="cd-qty">
                        <button data-act="dec">−</button>
                        <span>${it.qty}</span>
                        <button data-act="inc">+</button>
                    </div>
                    <div class="cd-pr">€${(it.qty * it.price).toFixed(2).replace('.', ',')}</div>
                    <button class="cd-rm" data-act="rm">×</button>
                </div>`;
            }).join('');

            const sub = this.subtotal();
            const vat = sub - sub / 1.23;
            const ship = sub >= 60 ? 0 : 4.90;
            const tot = sub + ship;
            if (subEl)  subEl.textContent  = '€' + sub.toFixed(2).replace('.', ',');
            if (vatEl)  vatEl.textContent  = '€' + vat.toFixed(2).replace('.', ',');
            if (shipEl) shipEl.textContent = ship === 0 ? 'zadarmo' : '€' + ship.toFixed(2).replace('.', ',');
            if (totEl)  totEl.textContent  = '€' + tot.toFixed(2).replace('.', ',');
        },
    };

    // Drawer open/close
    function openCart() {
        document.body.classList.add('cd-open');
        Cart.renderDrawer();
    }
    function closeCart() {
        document.body.classList.remove('cd-open');
    }

    document.addEventListener('click', (e) => {
        // Open via header click
        const open = e.target.closest('[data-cart-open]');
        if (open) { e.preventDefault(); openCart(); return; }

        // Close via backdrop or close button
        const close = e.target.closest('[data-cart-close]');
        if (close) { closeCart(); return; }

        // Add to cart
        const add = e.target.closest('[data-cart-add]');
        if (add) {
            e.preventDefault();
            try {
                const data = JSON.parse(add.dataset.product);
                Cart.add({
                    id: String(data.id),
                    code: data.code,
                    name: data.name,
                    line: data.line,
                    volume: data.volume,
                    price: parseFloat(data.price),
                    swatch: data.swatch || null,
                });
                openCart();
            } catch (err) { console.error(err); }
            return;
        }

        // Drawer item actions
        const it = e.target.closest('.cd-it');
        if (it) {
            const id = it.dataset.id;
            const act = e.target.dataset.act;
            if (act === 'inc') Cart.update(id, (Cart.find(id)?.qty || 1) + 1);
            else if (act === 'dec') Cart.update(id, Math.max(1, (Cart.find(id)?.qty || 1) - 1));
            else if (act === 'rm')  Cart.remove(id);
        }
    });

    // ESC key closes drawer
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeCart();
    });

    Cart.load();
    document.addEventListener('DOMContentLoaded', () => Cart.render());

    // Keep tabs in sync — reload the cart when another tab writes to it.
    window.addEventListener('storage', (e) => {
        if (e.key === KEY) { Cart.load(); Cart.render(); Cart.emit(); }
    });

    window.PhCart = Cart;
})();
