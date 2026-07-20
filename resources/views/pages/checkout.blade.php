@extends('layouts.app')

@section('title', 'Pokladňa - PREVIA')

@section('content')

@php
    $isCompanyChecked = old('is_company_purchase', $b2b ? '1' : '0') === '1';
    $shipSameChecked = old('shipping_same_as_billing', '1') === '1';
    $companyShown = $b2b || $isCompanyChecked;
@endphp

<section class="shop-head">
    <div class="crumbs">
        <a href="{{ route('home') }}" style="color:inherit;text-decoration:none">PREVIA</a>
        <span class="sep">/</span>
        <a href="{{ route('cart.show') }}" style="color:inherit;text-decoration:none">Košík</a>
        <span class="sep">/</span>
        <span>Pokladňa</span>
    </div>
    <h1>Pokladňa<br><em>{{ $b2b ? '· objednávka pre salón' : '' }}</em></h1>
    <div class="meta">
        <div class="ds">Vyplň fakturačné a doručovacie údaje a vyber spôsob platby - dobierka alebo platba kartou online.</div>
    </div>
</section>

@if ($errors->any())
    <div style="padding: 16px 56px; background: #fdecec; color: #b91c1c; border-bottom: 1px solid var(--line); font-size: 14px;">
        <strong>Chyba pri odoslaní:</strong>
        <ul style="margin: 8px 0 0 0; padding: 0 0 0 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('checkout.store') }}" id="checkout-form">
    @csrf
    <input type="hidden" name="items" id="checkout-items" value="[]">

    <section class="checkout">
        <div class="ck-l">
            <div class="ck-grp">
                <h3>Kontaktné údaje</h3>
                <div class="ck-row">
                    <div class="ck-fi">
                        <label>Meno a priezvisko</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', $b2b->contact_name ?? '') }}" required>
                    </div>
                    <div class="ck-fi">
                        <label>E-mail</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email', $b2b->email ?? '') }}" required>
                    </div>
                </div>
                <div class="ck-row">
                    <div class="ck-fi">
                        <label>Telefón</label>
                        <input type="tel" name="customer_phone" value="{{ old('customer_phone', $b2b->phone ?? '') }}" required>
                    </div>
                </div>
            </div>

            @unless ($b2b)
                <div class="ck-grp">
                    <label class="ck-toggle">
                        <input type="hidden" name="is_company_purchase" value="0">
                        <input type="checkbox" name="is_company_purchase" value="1" id="is-company-toggle" @checked($isCompanyChecked)>
                        <span>Nakupujem na firmu</span>
                    </label>
                </div>
            @else
                <input type="hidden" name="is_company_purchase" value="1">
            @endunless

            <div class="ck-grp" id="company-fields" @unless($companyShown) hidden @endunless>
                <h3>Firemné údaje</h3>
                <div class="ck-row">
                    <div class="ck-fi">
                        <label>Názov firmy / salónu</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $b2b->salon_name ?? '') }}">
                    </div>
                </div>
                <div class="ck-row">
                    <div class="ck-fi">
                        <label>IČO</label>
                        <input type="text" name="ico" value="{{ old('ico', $b2b->ico ?? '') }}">
                    </div>
                    <div class="ck-fi">
                        <label>IČ DPH (voliteľné)</label>
                        <input type="text" name="vat_id" value="{{ old('vat_id', $b2b->vat_id ?? '') }}">
                    </div>
                </div>
            </div>

            <div class="ck-grp" id="billing-address">
                <h3>Fakturačná / korešpondenčná adresa</h3>
                <div class="ck-row">
                    <div class="ck-fi">
                        <label>Ulica a číslo</label>
                        <input type="text" name="billing_address" value="{{ old('billing_address', $b2b->address ?? '') }}">
                    </div>
                </div>
                <div class="ck-row">
                    <div class="ck-fi">
                        <label>Mesto</label>
                        <input type="text" name="billing_city" value="{{ old('billing_city', $b2b->city ?? '') }}">
                    </div>
                    <div class="ck-fi" style="max-width:160px">
                        <label>PSČ</label>
                        <input type="text" name="billing_zip" value="{{ old('billing_zip', $b2b->zip ?? '') }}">
                    </div>
                    <div class="ck-fi" style="max-width:140px">
                        <label>Krajina</label>
                        <select name="billing_country">
                            <option value="SK" @selected(old('billing_country', $b2b->country ?? 'SK') === 'SK')>Slovensko</option>
                            <option value="CZ" @selected(old('billing_country', $b2b->country ?? 'SK') === 'CZ')>Česko</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="ck-grp" id="delivery-address-grp">
                <h3>Doručovacia adresa</h3>
                <div id="ship-same-wrap" style="margin-bottom:16px">
                    <label class="ck-toggle">
                        <input type="hidden" name="shipping_same_as_billing" value="0">
                        <input type="checkbox" name="shipping_same_as_billing" value="1" id="ship-same-toggle" @checked($shipSameChecked)>
                        <span>Doručiť na rovnakú adresu ako fakturačná</span>
                    </label>
                </div>
                <div id="shipping-fields" @if($shipSameChecked) hidden @endif>
                    <div class="ck-row">
                        <div class="ck-fi">
                            <label>Ulica a číslo</label>
                            <input type="text" name="shipping_address" value="{{ old('shipping_address') }}">
                        </div>
                    </div>
                    <div class="ck-row">
                        <div class="ck-fi">
                            <label>Mesto</label>
                            <input type="text" name="shipping_city" value="{{ old('shipping_city') }}">
                        </div>
                        <div class="ck-fi" style="max-width:160px">
                            <label>PSČ</label>
                            <input type="text" name="shipping_zip" value="{{ old('shipping_zip') }}">
                        </div>
                        <div class="ck-fi" style="max-width:140px">
                            <label>Krajina</label>
                            <select name="shipping_country">
                                <option value="SK" @selected(old('shipping_country', 'SK') === 'SK')>Slovensko</option>
                                <option value="CZ" @selected(old('shipping_country', 'SK') === 'CZ')>Česko</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ck-grp">
                <h3>Doprava</h3>
                @if (!empty($packetaKey))
                <div class="ck-method">
                    <input type="radio" id="dv-zas" name="delivery_choice" value="zasielkovna">
                    <label for="dv-zas">
                        <strong>Zásielkovňa – výdajné miesto</strong>
                        <small>Vyzdvihnutie na výdajnom mieste. Zadarmo od €60.</small>
                    </label>
                </div>
                @endif
                <div class="ck-method">
                    <input type="radio" id="dv-gls" name="delivery_choice" value="gls" checked>
                    <label for="dv-gls">
                        <strong>GLS – doručenie na adresu</strong>
                        <small>Doručenie kuriérom GLS v rámci SR. Zadarmo od €60.</small>
                    </label>
                </div>
                <div class="ck-method">
                    <input type="radio" id="dv-dpd" name="delivery_choice" value="dpd">
                    <label for="dv-dpd">
                        <strong>DPD – doručenie na adresu</strong>
                        <small>Doručenie kuriérom DPD v rámci SR. Zadarmo od €60.</small>
                    </label>
                </div>
                @if (!empty($packetaKey))
                <div id="pickup-point-block" hidden style="margin:4px 0 8px;padding:14px;border:1px solid var(--line)">
                    <button type="button" class="btn" id="pickup-pick-btn" style="margin-bottom:10px">Vybrať výdajné miesto →</button>
                    <div id="pickup-selected" style="font-size:14px;color:var(--ink)" hidden></div>
                    <input type="hidden" name="pickup_point_id" id="pickup_point_id" value="{{ old('pickup_point_id') }}">
                    <input type="hidden" name="pickup_point_name" id="pickup_point_name" value="{{ old('pickup_point_name') }}">
                    <input type="hidden" name="shipping_city" id="pickup_city" value="{{ old('shipping_city') }}">
                    <input type="hidden" name="shipping_zip" id="pickup_zip" value="{{ old('shipping_zip') }}">
                    <input type="hidden" name="shipping_country" id="pickup_country" value="SK">
                </div>
                @endif
            </div>

            <div class="ck-grp">
                <h3>Platba</h3>
                <div class="ck-method">
                    <input type="radio" id="pay-cod" name="payment_method" value="cod" checked>
                    <label for="pay-cod">
                        <strong>Dobierka</strong>
                        <small>Platba pri prevzatí kuriérovi. Žiadny príplatok.</small>
                    </label>
                </div>
                <div class="ck-method">
                    <input type="radio" id="pay-card" name="payment_method" value="card">
                    <label for="pay-card">
                        <strong>Online platba kartou</strong>
                        <small>Visa / Mastercard cez Stripe. Po odoslaní budete presmerovaní na bezpečnú platobnú bránu.</small>
                    </label>
                </div>
                @if (!empty($canPayByInvoice))
                <div class="ck-method">
                    <input type="radio" id="pay-transfer" name="payment_method" value="transfer">
                    <label for="pay-transfer">
                        <strong>Platba na faktúru (prevodom)</strong>
                        <small>Pre salóny. Vystavíme faktúru so splatnosťou (14 dní) a QR kódom. Objednávku odošleme a faktúru uhradíte prevodom do splatnosti.</small>
                    </label>
                </div>
                @endif
            </div>

            <div class="ck-grp">
                <h3>Poznámka k objednávke (voliteľné)</h3>
                <div class="ck-row">
                    <div class="ck-fi">
                        <textarea name="notes" rows="3" placeholder="Ak chcete niečo doplniť…">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <aside class="ck-r">
            <h3>Súhrn objednávky</h3>
            <div class="ck-summary" data-checkout-summary>
                <div class="ck-empty">Načítavam košík…</div>
            </div>
            <div class="ck-totals">
                <div class="ck-trow"><span>Medzisúčet</span><strong data-ck-sub>€0,00</strong></div>
                <div class="ck-trow" style="font-size:12px;color:var(--mute)"><span>z toho DPH 23 %</span><span data-ck-vat>€0,00</span></div>
                @if ($b2b && $b2b->discount_pct > 0)
                    <div class="ck-trow ck-trow--discount"><span>Zľava pre salón (−{{ $b2b->discount_pct }} %)</span><strong data-ck-disc>−€0,00</strong></div>
                @endif
                <div class="ck-trow"><span>Doprava</span><strong data-ck-ship>€0,00</strong></div>
                <div class="ck-trow ck-trow--big"><span>Celkom</span><strong data-ck-tot>€0,00</strong></div>
            </div>
            <button type="submit" class="btn ck-submit" id="ck-submit">Odoslať objednávku - dobierka</button>
            <p class="ck-note">Odoslaním súhlasíte s obchodnými podmienkami a spracovaním osobných údajov.</p>
        </aside>
    </section>
</form>

@push('scripts')
@if (!empty($packetaKey))
<script src="https://widget.packeta.com/v6/www/js/library.js"></script>
@endif
<script>
(function () {
    const DISCOUNT_PCT = {{ $b2b->discount_pct ?? 0 }};
    function fmt(n) { return '€' + n.toFixed(2).replace('.', ','); }
    // Item data comes from localStorage / admin-set product names — escape before render.
    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function render() {
        const c = window.PhCart;
        if (!c) return;
        const summary = document.querySelector('[data-checkout-summary]');
        if (c.items.length === 0) {
            summary.innerHTML = '<div class="ck-empty">Košík je prázdny. <a href="{{ route('shop.index') }}">Späť do eshopu</a></div>';
            document.querySelector('.ck-submit').disabled = true;
            return;
        }

        summary.innerHTML = c.items.map(it => `
            <div class="ck-li">
                <div class="ck-li-info">
                    <div class="ck-li-name">${esc(it.name)}</div>
                    <div class="ck-li-meta">${esc(it.qty)} × ${fmt(it.price)}</div>
                </div>
                <div class="ck-li-pr">${fmt(it.qty * it.price)}</div>
            </div>
        `).join('');

        const sub = c.subtotal();
        const vat = sub - sub / 1.23;
        const ship = sub >= 60 ? 0 : 4.90;
        const tot = sub + ship;
        document.querySelector('[data-ck-sub]').textContent = fmt(sub);
        document.querySelector('[data-ck-vat]').textContent = fmt(vat);
        const disc = document.querySelector('[data-ck-disc]');
        if (disc) {
            const original = c.items.reduce((s, i) => s + (i.price / (1 - DISCOUNT_PCT / 100)) * i.qty, 0);
            disc.textContent = '−' + fmt(original - sub);
        }
        document.querySelector('[data-ck-ship]').textContent = ship === 0 ? 'zadarmo' : fmt(ship);
        document.querySelector('[data-ck-tot]').textContent = fmt(tot);

        document.getElementById('checkout-items').value = JSON.stringify(c.items.map(i => ({ id: String(i.id), qty: i.qty })));
    }

    document.addEventListener('cart:change', render);
    document.addEventListener('DOMContentLoaded', render);

    function updateSubmitLabel() {
        const method = document.querySelector('input[name="payment_method"]:checked')?.value;
        const btn = document.getElementById('ck-submit');
        if (!btn) return;
        btn.textContent = method === 'card' ? 'Pokračovať na platbu kartou →'
            : (method === 'transfer' ? 'Odoslať objednávku - platba na faktúru' : 'Odoslať objednávku - dobierka');
    }
    document.querySelectorAll('input[name="payment_method"]').forEach(el => el.addEventListener('change', updateSubmitLabel));
    document.addEventListener('DOMContentLoaded', updateSubmitLabel);

    // Toggles: company fields (IČO / DPH) + delivery same as correspondence address
    const companyToggle = document.getElementById('is-company-toggle');
    const companyFields = document.getElementById('company-fields');
    const shipSameToggle = document.getElementById('ship-same-toggle');
    const shippingFields = document.getElementById('shipping-fields');

    function syncCompany() {
        if (!companyToggle || !companyFields) return; // B2B: always company
        companyFields.hidden = !companyToggle.checked;
    }
    function syncShipSame() {
        if (!shipSameToggle) return;
        shippingFields.hidden = shipSameToggle.checked;
    }
    companyToggle?.addEventListener('change', syncCompany);
    shipSameToggle?.addEventListener('change', syncShipSame);

    // --- Doprava: adresa vs výdajné miesto (Packeta / Zásielkovňa) ---
    const PACKETA_KEY = @json($packetaKey ?? null);
    const addressGrp   = document.getElementById('delivery-address-grp');
    const pickupBlock  = document.getElementById('pickup-point-block');
    const pickupBtn    = document.getElementById('pickup-pick-btn');
    const pickupSelected = document.getElementById('pickup-selected');

    // Enable/disable a block's inputs so only the active delivery mode submits
    // (address and pickup share shipping_city/zip/country field names).
    function setInputs(container, enabled) {
        if (!container) return;
        container.querySelectorAll('input, select').forEach(el => { el.disabled = !enabled; });
    }

    function isPickupChoice() {
        return document.querySelector('input[name="delivery_choice"]:checked')?.value === 'zasielkovna';
    }
    function syncShippingMethod() {
        const pickup = isPickupChoice();
        if (addressGrp) { addressGrp.hidden = pickup; setInputs(addressGrp, !pickup); }
        if (pickupBlock) { pickupBlock.hidden = !pickup; setInputs(pickupBlock, pickup); }
    }
    document.querySelectorAll('input[name="delivery_choice"]').forEach(el => el.addEventListener('change', syncShippingMethod));
    document.addEventListener('DOMContentLoaded', syncShippingMethod);

    function showPickup(point) {
        document.getElementById('pickup_point_id').value = point.id ?? '';
        document.getElementById('pickup_point_name').value = point.name ?? '';
        document.getElementById('pickup_city').value = point.city ?? '';
        document.getElementById('pickup_zip').value = (point.zip ?? '').toString().replace(/\s+/g, '');
        if (pickupSelected) {
            pickupSelected.hidden = false;
            pickupSelected.textContent = 'Vybrané: ' + (point.name ?? '') + (point.city ? ', ' + point.city : '');
        }
    }

    pickupBtn?.addEventListener('click', function () {
        if (!window.Packeta || !PACKETA_KEY) { alert('Výber výdajného miesta momentálne nie je dostupný.'); return; }
        window.Packeta.Widget.pick(PACKETA_KEY, function (point) {
            if (point) showPickup(point);
        }, { language: 'sk', country: 'sk,cz' });
    });

    // Block submit if Zásielkovňa is chosen but no pickup point was selected.
    document.getElementById('checkout-form')?.addEventListener('submit', function (e) {
        if (isPickupChoice() && !document.getElementById('pickup_point_id')?.value) {
            e.preventDefault();
            alert('Prosím vyberte výdajné miesto.');
        }
    });
})();
</script>
@endpush

@endsection
