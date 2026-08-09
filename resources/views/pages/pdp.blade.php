@extends('layouts.app')

@section('title', $product->name . ' - PREVIA')
@section('description', $product->subtitle)

@section('content')

@php
    $line = $product->line;
    $defaultIngredients = [
        ['n' => '01', 'name' => trim('Aktívny komplex ' . ($product->complex ?? '')), 'small' => 'Talianske laboratórium · patent', 'pct' => '1,8 %'],
        ['n' => '02', 'name' => 'Botanické zložky', 'small' => 'Lisované za studena · prírodné', 'pct' => '2,4 %'],
        ['n' => '03', 'name' => 'Pantenol (B5)', 'small' => 'Preniká do kortexu', 'pct' => '1,2 %'],
        ['n' => '04', 'name' => 'Mierny tenzid', 'small' => 'Rastlinného pôvodu, biodegradovateľné', 'pct' => '8,0 %'],
    ];
    $defaultResults = [
        ['label' => 'Lesk vlasu', 'small' => 'Glossmeter Samba T2', 'value' => '+47%', 'fill' => 78],
        ['label' => 'Pevnosť vlákna', 'small' => 'Tahový test', 'value' => '+34%', 'fill' => 62],
        ['label' => 'Zníženie lámavosti', 'small' => 'Po 200 česaniach', 'value' => '−68%', 'fill' => 88],
        ['label' => 'Hydratácia', 'small' => 'Spotrebiteľská anketa', 'value' => '+92%', 'fill' => 94],
    ];
    $defaultCompat = [
        ['ok' => true,  'name' => 'Po blondovaní / odfarbovaní', 'small' => 'Obnova proteínovej štruktúry', 'value' => 'áno'],
        ['ok' => true,  'name' => 'Po keratínovom narovnávaní', 'small' => 'Predĺži životnosť úpravy', 'value' => 'áno'],
        ['ok' => true,  'name' => 'Termické poškodenie', 'small' => 'Kulma, žehlička, sušič', 'value' => 'áno'],
        ['ok' => false, 'name' => 'Pre veľmi mastné vlasy', 'small' => 'Skús detoxikačnú líniu', 'value' => 'nie'],
    ];
    $defaultProtocol = [
        ['step' => 'krok 01', 'title' => 'Predumývanie', 'desc' => 'Pred aplikáciou - detoxikačný šampón raz týždenne pre odstránenie nánosov.'],
        ['step' => 'krok 02', 'title' => 'Aplikácia',    'desc' => 'Veľkosť eura na mokré vlasy. Masírujte 60 sekúnd v pokožke hlavy.'],
        ['step' => 'krok 03', 'title' => 'Druhé umytie', 'desc' => 'Zopakujte - prvé umytie čistí, druhé pôsobí. Nechajte stáť 90 sekúnd.'],
        ['step' => 'krok 04', 'title' => 'Po-aplikácia', 'desc' => 'Doplňte maskou alebo olejom z rovnakej línie.'],
    ];
    $ingredients = $product->ingredients ?: $defaultIngredients;
    $results     = $product->results ?: $defaultResults;
    $compatibility = $product->compatibility ?: $defaultCompat;
    $protocol    = $product->protocol ?: $defaultProtocol;
@endphp

<section class="pdp{{ ($product->hasShades() && auth('b2b')->check()) ? ' pdp--shades' : '' }}">
    <div class="pdp-l">
        <div class="crosshair"></div>
        <div class="corners"><span class="tl"></span><span class="tr"></span><span class="bl"></span><span class="br"></span></div>
        <div class="imgmeta">
            FORMULÁCIA · {{ $product->complex ?: '-' }}<br>
            pH · 5,4<br>
            <strong>{{ $product->volume }}</strong>
        </div>
        <div class="b-main">
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:contain;display:block;padding:8%;box-sizing:border-box">
            @else
                @include('partials.bottle', ['kind' => $product->kind, 'tone' => $product->tone, 'cap' => $product->cap ?: $product->tone, 'sub' => $product->complex ?: 'Made in Italy', 'n' => $product->code, 'label' => 'PREVIA'])
            @endif
        </div>
    </div>

    <div class="pdp-r">
        <div class="crumbs">
            <a href="{{ route('home') }}" style="color:inherit;text-decoration:none">PREVIA</a>
            <span class="sep">/</span>
            <a href="{{ route('shop.index') }}" style="color:inherit;text-decoration:none">Eshop</a>
            <span class="sep">/</span>
            <span>{{ $line?->name ?? $product->line_label }}</span>
            <span class="sep">/</span>
            <span>{{ $product->name }}</span>
        </div>
        <div class="line">- línia {{ $line?->code ?? '-' }} · {{ $line?->name ?? $product->line_label }} · {{ $line?->eyebrow ?? '' }}</div>
        <h1>{{ $product->name }}</h1>

        @php
            $b2bPdp = auth('b2b')->user();
            // Product sale first (everyone), then B2B salon discount on top.
            $basePdp = $product->salePrice();
            $effPrice = $b2bPdp && $b2bPdp->discount_pct > 0 ? round($basePdp * (1 - $b2bPdp->discount_pct / 100), 2) : $basePdp;
            $effNet = round($effPrice / (1 + \App\Models\Product::VAT_RATE), 2);
            $showStrikePdp = $effPrice < (float) $product->price;
            $netPrimaryPdp = $b2bPdp && !empty($b2bPdp->vat_id);
            $vol = $product->volume;
            // Per-100ml only for a single "<n> ml" volume — skip multipacks
            // ("10 × 5 ml"), gram weights, sets ("Sada"), and "-".
            $perUnit = preg_match('/^\s*(\d+)\s*ml\s*$/i', (string) $vol, $m) && (int) $m[1] > 0
                ? '€' . number_format(($effPrice / (int) $m[1]) * 100, 2, ',', ' ') . ' / 100 ml'
                : '';
        @endphp

        <div class="pr-row">
            <div class="pr-wrap">
                <div class="pr">
                    @if($netPrimaryPdp)
                        €{{ number_format($effNet, 2, ',', ' ') }} <span class="pr-vat">bez DPH</span>
                    @else
                        €{{ number_format($effPrice, 2, ',', ' ') }} <span class="pr-vat">s DPH</span>
                        @if($showStrikePdp)<s style="color:var(--mute);font-size:0.6em;margin-left:6px">€{{ number_format($product->price, 2, ',', ' ') }}</s>@endif
                    @endif
                </div>
                <div class="pr-sub">
                    @if($netPrimaryPdp)
                        s DPH €{{ number_format($effPrice, 2, ',', ' ') }}
                    @else
                        bez DPH €{{ number_format($effNet, 2, ',', ' ') }}
                    @endif
                </div>
            </div>
            <div class="pr-meta">{{ $vol }}</div>
        </div>

        @php
            $payloadPdp = [
                'id'      => (string) $product->id,
                'code'    => $product->code,
                'name'    => $product->name,
                'line'    => $product->line_label,
                'volume'  => $vol,
                'price'   => $effPrice,
            ];
            $hasShades = $product->hasShades();
            $shadesGrouped = $hasShades ? $product->shadesGrouped() : [];
            $shadeMode = $hasShades && $b2bPdp;
        @endphp

        @if ($shadeMode)
            @php
                $totalShades = collect($shadesGrouped)->sum(fn ($s) => count($s));
            @endphp
            <div class="pdp-shades" data-pdp-shades>
                <div class="pdp-shades-toolbar">
                    <div class="pdp-shades-meta">
                        <strong>{{ $totalShades }}</strong> odtieňov
                        @if ($b2bPdp && $b2bPdp->discount_pct > 0)
                            <span class="dot">·</span> tvoja zľava <strong>−{{ $b2bPdp->discount_pct }} %</strong>
                        @endif
                    </div>
                    <div class="pdp-shades-tools">
                        <input type="search" class="pdp-shades-search" placeholder="Hľadať kód alebo názov…" aria-label="Hľadať odtieň">
                        <div class="pdp-shades-views" role="tablist" aria-label="Zobrazenie odtieňov">
                            <button type="button" data-shade-view="grid" class="on" aria-label="Mriežka" title="Mriežka">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="1" y="1" width="5" height="5"/><rect x="8" y="1" width="5" height="5"/><rect x="1" y="8" width="5" height="5"/><rect x="8" y="8" width="5" height="5"/></svg>
                            </button>
                            <button type="button" data-shade-view="list" aria-label="Zoznam" title="Zoznam">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.4"><line x1="1" y1="3" x2="13" y2="3"/><line x1="1" y1="7" x2="13" y2="7"/><line x1="1" y1="11" x2="13" y2="11"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pdp-shades-body" data-shade-view-mode="grid">
                    @foreach ($shadesGrouped as $groupKey => $shadesInGroup)
                        @php
                            $cnt = count($shadesInGroup);
                            $word = $cnt === 1 ? 'odtieň' : ($cnt < 5 ? 'odtiene' : 'odtieňov');
                            $isIntKey = is_int($groupKey);
                        @endphp
                        <div class="pdp-shades-section" data-level="{{ $isIntKey ? $groupKey : '' }}" data-group="{{ $isIntKey ? '' : (string) $groupKey }}">
                            <div class="pdp-shades-head">
                                @if ($isIntKey)
                                    <span class="lv">{{ $groupKey }}</span>
                                @else
                                    <span class="gr">{{ $groupKey }}</span>
                                @endif
                                <span class="ct">· {{ $cnt }} {{ $word }}</span>
                            </div>
                            <div class="pdp-shades-items">
                                @foreach ($shadesInGroup as $shade)
                                    @php
                                        $sCode = $shade['code'] ?? '';
                                        $sName = $shade['name'] ?? '';
                                        $sColor = $shade['color'] ?? null;
                                        $sPriceRaw = isset($shade['price']) && $shade['price'] !== null && $shade['price'] !== ''
                                            ? (float) $shade['price']
                                            : (float) $product->price;
                                        // Apply the product sale to the shade base, then B2B discount.
                                        $sPriceBase = $product->discountedPrice($sPriceRaw);
                                        $sPrice = $b2bPdp && $b2bPdp->discount_pct > 0
                                            ? round($sPriceBase * (1 - $b2bPdp->discount_pct / 100), 2)
                                            : $sPriceBase;
                                        $shadePayload = [
                                            'id'     => $product->id . '-' . $sCode,
                                            'code'   => $sCode,
                                            'name'   => trim($product->name . ' · ' . $sCode . ($sName ? ' ' . $sName : '')),
                                            'line'   => $product->line_label,
                                            'volume' => $vol,
                                            'price'  => $sPrice,
                                            'swatch' => $sColor,
                                        ];
                                    @endphp
                                    <div class="pdp-shade" data-shade
                                         data-code="{{ $sCode }}"
                                         data-name="{{ $sName }}"
                                         data-product="{{ json_encode($shadePayload, JSON_UNESCAPED_UNICODE) }}">
                                        <div class="pdp-shade-swatch" @if($sColor) style="background:{{ $sColor }}" @else style="background:linear-gradient(135deg,#d9d3c8,#a7a097)" @endif>
                                            <span class="pdp-shade-badge" data-q-badge>0</span>
                                        </div>
                                        <div class="pdp-shade-info">
                                            <div class="pdp-shade-code">{{ $sCode }}</div>
                                            <div class="pdp-shade-name">{{ $sName }}</div>
                                        </div>
                                        <div class="pdp-shade-pr">€{{ number_format($sPrice, 2, ',', ' ') }}</div>
                                        <div class="pdp-shade-qty">
                                            <button type="button" data-q-dec aria-label="Znížiť">−</button>
                                            <input type="number" min="0" max="99" value="0" data-q aria-label="Množstvo">
                                            <button type="button" data-q-inc aria-label="Zvýšiť">+</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pdp-shades-foot" data-shade-foot>
                    <div class="pdp-shades-foot-info">
                        Vybrané: <strong data-shade-count>0</strong> ks
                        <span class="sep">·</span>
                        <strong data-shade-tot>€0,00</strong>
                    </div>
                    <button type="button" class="btn" data-shade-add disabled @if($product->isOutOfStock()) data-oos @endif>
                        @if ($product->isOutOfStock()) Vypredané @else Pridať do košíka <span aria-hidden="true">→</span> @endif
                    </button>
                </div>
            </div>

            @push('scripts')
            <script>
            (function () {
                const root = document.querySelector('[data-pdp-shades]');
                if (!root) return;
                const body = root.querySelector('.pdp-shades-body');
                const search = root.querySelector('.pdp-shades-search');
                const foot = root.querySelector('[data-shade-foot]');
                const countEl = root.querySelector('[data-shade-count]');
                const totEl = root.querySelector('[data-shade-tot]');
                const addBtn = root.querySelector('[data-shade-add]');
                const fmt = (n) => '€' + Number(n).toFixed(2).replace('.', ',');

                const VIEW_KEY = 'ph_shade_view';
                function applyView(v) {
                    body.setAttribute('data-shade-view-mode', v);
                    root.querySelectorAll('[data-shade-view]').forEach(b => b.classList.toggle('on', b.dataset.shadeView === v));
                    try { localStorage.setItem(VIEW_KEY, v); } catch (e) {}
                }
                applyView(localStorage.getItem(VIEW_KEY) || 'grid');
                root.querySelectorAll('[data-shade-view]').forEach(b => b.addEventListener('click', () => applyView(b.dataset.shadeView)));

                function recompute() {
                    let count = 0, tot = 0;
                    root.querySelectorAll('[data-shade]').forEach(el => {
                        const q = parseInt(el.querySelector('[data-q]').value, 10) || 0;
                        const data = JSON.parse(el.dataset.product);
                        const badge = el.querySelector('[data-q-badge]');
                        if (q > 0) {
                            count += q;
                            tot += q * parseFloat(data.price);
                            el.classList.add('on');
                            if (badge) { badge.textContent = q; badge.classList.add('on'); }
                        } else {
                            el.classList.remove('on');
                            if (badge) { badge.textContent = '0'; badge.classList.remove('on'); }
                        }
                    });
                    countEl.textContent = count;
                    totEl.textContent = fmt(tot);
                    foot.classList.toggle('on', count > 0);
                    addBtn.disabled = count === 0 || addBtn.hasAttribute('data-oos');
                }
                recompute();

                body.addEventListener('click', (e) => {
                    const dec = e.target.closest('[data-q-dec]');
                    const inc = e.target.closest('[data-q-inc]');
                    if (dec || inc) {
                        const card = (dec || inc).closest('[data-shade]');
                        const input = card.querySelector('[data-q]');
                        let v = parseInt(input.value, 10) || 0;
                        v = dec ? Math.max(0, v - 1) : Math.min(99, v + 1);
                        input.value = v;
                        recompute();
                        return;
                    }
                    const sw = e.target.closest('.pdp-shade-swatch');
                    if (sw && body.getAttribute('data-shade-view-mode') === 'grid') {
                        const card = sw.closest('[data-shade]');
                        const input = card.querySelector('[data-q]');
                        let v = parseInt(input.value, 10) || 0;
                        v = Math.min(99, v + 1);
                        input.value = v;
                        recompute();
                    }
                });

                body.addEventListener('input', (e) => {
                    if (!e.target.matches('[data-q]')) return;
                    let v = parseInt(e.target.value, 10);
                    if (isNaN(v) || v < 0) v = 0;
                    if (v > 99) v = 99;
                    e.target.value = v;
                    recompute();
                });

                search.addEventListener('input', () => {
                    const q = search.value.trim().toLowerCase();
                    root.querySelectorAll('.pdp-shades-section').forEach(sec => {
                        let anyVisible = false;
                        sec.querySelectorAll('[data-shade]').forEach(card => {
                            const matchCode = card.dataset.code.toLowerCase().includes(q);
                            const matchName = (card.dataset.name || '').toLowerCase().includes(q);
                            const matchLevel = sec.dataset.level === q;
                            const v = q === '' || matchCode || matchName || matchLevel;
                            card.style.display = v ? '' : 'none';
                            if (v) anyVisible = true;
                        });
                        sec.style.display = (q === '' || anyVisible) ? '' : 'none';
                    });
                });

                addBtn.addEventListener('click', () => {
                    if (!window.PhCart || addBtn.disabled) return;
                    let total = 0;
                    root.querySelectorAll('[data-shade]').forEach(el => {
                        const q = parseInt(el.querySelector('[data-q]').value, 10) || 0;
                        if (q <= 0) return;
                        const data = JSON.parse(el.dataset.product);
                        window.PhCart.add({
                            id: String(data.id),
                            code: data.code,
                            name: data.name,
                            line: data.line,
                            volume: data.volume,
                            price: parseFloat(data.price),
                            swatch: data.swatch || null,
                            qty: q,
                        });
                        el.querySelector('[data-q]').value = 0;
                        total += q;
                    });
                    recompute();
                    if (typeof window.PhCart.flash === 'function') {
                        window.PhCart.flash(total + ' ks pridaných do košíka');
                    }
                });
            })();
            </script>
            @endpush
        @elseif ($hasShades)
            <div class="pdp-shades-locked">
                <p>Tento produkt obsahuje <strong>{{ collect($shadesGrouped)->sum(fn ($s) => count($s)) }}</strong> odtieňov dostupných pre profesionálnych partnerov.</p>
                <a href="{{ route('b2b.login') }}" class="btn">Prihlásiť sa pre salóny →</a>
            </div>
        @else
            @php $sizeVariants = ($variants ?? collect())->filter(fn ($v) => $v->id === $product->id || $v->volume); @endphp
            <h4>Veľkosť</h4>
            <div class="opts opts--sizes">
                @if($sizeVariants->count() > 1)
                    @foreach($sizeVariants as $v)
                        @php $locked = $v->b2b_only && !$b2bPdp; @endphp
                        @if($v->id === $product->id)
                            <button type="button" class="opt on" disabled>{{ $v->volume }}</button>
                        @elseif($locked)
                            <a href="{{ route('b2b.login') }}" class="opt opt--locked" title="Väčšie balenie je dostupné pre salóny po prihlásení">{{ $v->volume }}<span class="opt-tag">salón</span></a>
                        @else
                            <a href="{{ route('product.show', $v->slug) }}" class="opt">{{ $v->volume }}</a>
                        @endif
                    @endforeach
                @else
                    <button type="button" class="opt on" disabled>{{ $vol }}</button>
                @endif
            </div>

            <div class="qty-cta">
                <div class="qty">
                    <button type="button" id="pdp-qty-dec">−</button>
                    <div class="v" id="pdp-qty-val">1</div>
                    <button type="button" id="pdp-qty-inc">+</button>
                </div>
                @if ($product->isOutOfStock())
                    <button type="button" class="btn" disabled>Vypredané</button>
                @else
                    <button type="button" class="btn" id="pdp-add" data-product="{{ json_encode($payloadPdp, JSON_UNESCAPED_UNICODE) }}">
                        Pridať do košíka <span aria-hidden="true">→</span>
                    </button>
                @endif
            </div>
            @push('scripts')
            <script>
            (function () {
                const qtyVal = document.getElementById('pdp-qty-val');
                const addBtn = document.getElementById('pdp-add');
                if (!addBtn) return;

                let qty = 1;
                const product = JSON.parse(addBtn.dataset.product);

                document.getElementById('pdp-qty-dec').addEventListener('click', () => { qty = Math.max(1, qty - 1); qtyVal.textContent = qty; });
                document.getElementById('pdp-qty-inc').addEventListener('click', () => { qty = Math.min(99, qty + 1); qtyVal.textContent = qty; });

                addBtn.addEventListener('click', () => {
                    if (window.PhCart) {
                        window.PhCart.add({ ...product, qty });
                    }
                });
            })();
            </script>
            @endpush
        @endif

        <div class="meta-list">
            <div class="li"><div class="k">Doručenie</div><div class="v">Expresne v rámci SR · zadarmo od €60</div><div class="ic">→</div></div>
            <div class="li"><div class="k">Vrátenie</div><div class="v">30 dní · bez otázok</div><div class="ic">→</div></div>
            {{-- <div class="li"><div class="k">Diagnostika</div><div class="v">Pasuje to k vašim vlasom?</div><div class="ic">→</div></div> --}}
            <div class="li"><div class="k">Pre salóny</div><div class="v">Cena pre salóny · litrové balenie</div><div class="ic">→</div></div>
        </div>
    </div>
</section>

@if($product->description || $product->subtitle)
<section class="pdp-about">
    <div class="pdp-about-l">
        <div class="line">- O produkte</div>
    </div>
    <div class="pdp-about-r">
        <p>{{ $product->description ?: $product->subtitle }}</p>
    </div>
</section>
@endif

<section class="pdp-detail">
    <div class="col">
        <div class="line" style="margin-bottom:18px">- Aktívne zložky</div>
        <h3>Formulácia <span style="color:var(--mute);font-weight:200">{{ $product->complex ?: '' }}</span></h3>
        <p>Komplex zameraný na obnovu disulfidových väzieb v kortexe vlasu. Bez pridanej vody, parfumovaný esenciálnymi olejmi.</p>
        <div class="ing">
            @foreach($ingredients as $ing)
                <div class="ing-it">
                    <div class="n">{{ $ing['n'] }}</div>
                    <div class="nm">{{ $ing['name'] }}<small>{{ $ing['small'] }}</small></div>
                    <div class="pct">{{ $ing['pct'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="col">
        <div class="line" style="margin-bottom:18px">- Klinický test</div>
        <h3>Výsledky <span style="color:var(--mute);font-weight:200">po 28 dňoch</span></h3>
        <p>124 účastníkov · dvojito zaslepené · in vitro a spotrebiteľské testovanie · Univerzita v Bologni, 2024.</p>
        <div class="results">
            @foreach($results as $r)
                <div>
                    <div class="res-it"><div class="l">{{ $r['label'] }}<small>{{ $r['small'] }}</small></div><div class="v">{{ $r['value'] }}</div></div>
                    <div class="trial-bar"><div class="fill" style="width:{{ $r['fill'] }}%"></div></div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="col">
        <div class="line" style="margin-bottom:18px">- Kompatibilita</div>
        <h3>Pre koho <span style="color:var(--mute);font-weight:200">je toto?</span></h3>
        <p>{{ $product->name }} je formulovaný pre vlasy s konkrétnym profilom.</p>
        <div class="ing">
            @foreach($compatibility as $c)
                <div class="ing-it">
                    <div class="n">{{ $c['ok'] ? '✓' : '✕' }}</div>
                    <div class="nm">{{ $c['name'] }}<small>{{ $c['small'] }}</small></div>
                    <div class="pct">{{ $c['value'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="howto">
    <div class="section-head" style="border-bottom:none;padding-bottom:0;margin-bottom:32px">
        <h2 class="h2">Protokol - <em>ako používať.</em></h2>
        @php($pc = count($protocol))
        <div class="section-sub">{{ $pc }} {{ $pc === 1 ? 'krok' : ($pc < 5 ? 'kroky' : 'krokov') }} · ~6 min</div>
    </div>
    <div class="howto-grid">
        @foreach($protocol as $step)
            <div class="howto-step">
                <div class="n">{{ $step['step'] }}</div>
                <div class="ti">{{ $step['title'] }}</div>
                <div class="ds">{{ $step['desc'] }}</div>
            </div>
        @endforeach
    </div>
</section>

<section class="compat">
    <div class="section-head">
        <h2 class="h2">Doplňuje sa <em>s týmito.</em></h2>
        <div class="section-sub">{{ $crossSell->count() }} odporúčaných</div>
    </div>
    <div class="grid-4">
        @foreach($crossSell as $p)
            @include('partials.product-card', ['p' => $p])
        @endforeach
    </div>
</section>

@endsection
