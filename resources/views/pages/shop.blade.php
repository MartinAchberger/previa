@extends('layouts.app')

@section('title', 'Eshop - všetky línie · PREVIA')

@section('content')

@php
    $typeLabels = \App\Models\Product::typeLabels();

    $current = [
        'line' => $activeLine,
        'type' => $activeType,
        'sort' => $activeSort,
        'q'    => $searchTerm ?: null,
    ];
    $isB2bShop = auth('b2b')->check();
    $proKey = \App\Models\Product::PRO_FILTER;
    $toggleUrl = function ($key, $value) use ($current) {
        $params = $current;
        $params[$key] = ($params[$key] ?? null) === $value ? null : $value;
        return route('shop.index', array_filter($params, fn ($v) => $v !== null && $v !== ''));
    };
    $setSortUrl = function ($value) use ($current) {
        $params = $current;
        $params['sort'] = $value;
        return route('shop.index', array_filter($params, fn ($v) => $v !== null && $v !== ''));
    };
    $hasAnyFilter = (bool) ($activeLine || $activeType || $searchTerm);
    $filterCount = ($activeLine ? 1 : 0) + ($activeType ? 1 : 0) + ($searchTerm ? 1 : 0);
@endphp

<section class="shop-head">
    <div class="crumbs">
        <a href="{{ route('home') }}" style="color:inherit;text-decoration:none">PREVIA</a>
        <span class="sep">/</span>
        <span>Eshop</span>
    </div>
    @if($searchTerm)
        <h1>Hľadáte<br><em>„{{ $searchTerm }}“.</em></h1>
    @elseif($proFilter)
        <h1>Previa Pro<br><em>· všetko pre profesionála.</em></h1>
    @else
        <h1>Všetky<br><em>produkty.</em></h1>
    @endif
    <div class="meta">
        <div class="ds">Profesionálna vlasová kozmetika z Talianska · až 97 % prírodných ingrediencií · vegan &amp; cruelty-free.</div>
        <form action="{{ route('shop.index') }}" method="get" class="shop-search" role="search">
            @if($activeLine)<input type="hidden" name="line" value="{{ $activeLine }}">@endif
            @if($activeType)<input type="hidden" name="type" value="{{ $activeType }}">@endif
            <input type="search" name="q" value="{{ $searchTerm }}" placeholder="Hľadať produkt…" aria-label="Hľadať produkt" autocomplete="off">
            <button type="submit" aria-label="Hľadať">→</button>
        </form>
    </div>
</section>

<div class="shop-body">
    <aside class="shop-fil">
        <button type="button" class="shop-fil-toggle" data-fil-toggle aria-expanded="false">
            <span>Filtre{{ $hasAnyFilter ? ' (' . $filterCount . ')' : '' }}</span>
            <span class="chev" aria-hidden="true"></span>
        </button>
        <div class="fil-groups">
            <div class="grp {{ $activeLine ? 'open' : '' }}">
                <h4><button type="button" class="grp-head" data-grp-toggle aria-expanded="{{ $activeLine ? 'true' : 'false' }}">Línia <span class="chev" aria-hidden="true"></span></button></h4>
                <div class="grp-body">
                    @if($isB2bShop && $proCount > 0)
                        <a href="{{ $toggleUrl('line', $proKey) }}" class="opt opt--pro {{ $activeLine === $proKey ? 'on' : '' }}" style="text-decoration:none;color:inherit;display:flex">
                            <div class="box"></div>
                            <div class="lab">Previa Pro</div>
                            <span class="cnt">{{ str_pad($proCount, 2, '0', STR_PAD_LEFT) }}</span>
                        </a>
                    @endif
                    @foreach($lines as $line)
                        <a href="{{ $toggleUrl('line', $line->slug) }}" class="opt {{ $activeLine === $line->slug ? 'on' : '' }}" style="text-decoration:none;color:inherit;display:flex">
                            <div class="box"></div>
                            <div class="lab">{{ $line->name }}</div>
                            <span class="cnt">{{ str_pad($lineCounts[$line->id] ?? 0, 2, '0', STR_PAD_LEFT) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="grp {{ $activeType ? 'open' : '' }}">
                <h4><button type="button" class="grp-head" data-grp-toggle aria-expanded="{{ $activeType ? 'true' : 'false' }}">Typ produktu <span class="chev" aria-hidden="true"></span></button></h4>
                <div class="grp-body">
                    @if($isB2bShop && $proCount > 0)
                        <a href="{{ $toggleUrl('type', $proKey) }}" class="opt opt--pro {{ $activeType === $proKey ? 'on' : '' }}" style="text-decoration:none;color:inherit;display:flex">
                            <div class="box"></div>
                            <div class="lab">Previa Pro</div>
                            <span class="cnt">{{ str_pad($proCount, 2, '0', STR_PAD_LEFT) }}</span>
                        </a>
                    @endif
                    @foreach($typeLabels as $typeSlug => $typeLabel)
                        @if(($typeCounts[$typeSlug] ?? 0) > 0)
                            <a href="{{ $toggleUrl('type', $typeSlug) }}" class="opt {{ $activeType === $typeSlug ? 'on' : '' }}" style="text-decoration:none;color:inherit;display:flex">
                                <div class="box"></div>
                                <div class="lab">{{ $typeLabel }}</div>
                                <span class="cnt">{{ str_pad($typeCounts[$typeSlug], 2, '0', STR_PAD_LEFT) }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="grp shop-fil-quiz">
                <a href="{{ route('quiz.show') }}" class="shop-fil-quiz-link">
                    <span class="eyebrow-sm">Diagnostika · 60 sekúnd</span>
                    <strong>Nájdite rutinu pre svoje vlasy →</strong>
                </a>
            </div>
        </div>
    </aside>

    <div class="shop-list">
        @if($proFilter && !$searchTerm)
            <div class="line-intro">
                <div class="line-intro-eyebrow">Pre salóny</div>
                <h2>Previa Pro</h2>
                <p>Objav kompletnú profesionálnu ponuku PREVIA - od permanentných a bezamoniakových farieb cez zosvetľovacie produkty až po oxidanty a technickú starostlivosť pre každodennú prácu v salóne. <a href="{{ route('b2b.pro') }}" style="color:inherit">Prehľadne po kategóriách →</a></p>
            </div>
        @elseif($activeLineModel && $activeLineModel->description && $lines->contains('slug', $activeLine))
            <div class="line-intro">
                <div class="line-intro-eyebrow">{{ $activeLineModel->eyebrow ?: 'Línia' }}</div>
                <h2>{{ $activeLineModel->name }}</h2>
                <p>{{ $activeLineModel->description }}</p>
            </div>
        @endif
        <div class="shop-list-bar">
            <div class="chips">
                @if($searchTerm)
                    <a href="{{ $toggleUrl('q', $searchTerm) }}" class="chip" style="text-decoration:none;color:inherit">„{{ $searchTerm }}“ <span class="x">×</span></a>
                @endif
                @if($activeLine)
                    <a href="{{ $toggleUrl('line', $activeLine) }}" class="chip" style="text-decoration:none;color:inherit">{{ $activeLine === $proKey ? 'Previa Pro' : ($lines->firstWhere('slug', $activeLine)?->name ?? $activeLine) }} <span class="x">×</span></a>
                @endif
                @if($activeType)
                    <a href="{{ $toggleUrl('type', $activeType) }}" class="chip" style="text-decoration:none;color:inherit">{{ $activeType === $proKey ? 'Previa Pro' : ($typeLabels[$activeType] ?? $activeType) }} <span class="x">×</span></a>
                @endif
                @if($hasAnyFilter)
                    <a href="{{ route('shop.index', array_filter(['sort' => $activeSort])) }}" class="chip" style="background:transparent;color:var(--mute);text-decoration:none">Vyčistiť filtre</a>
                @endif
            </div>
            <div style="display:flex;gap:14px;align-items:center;font-size:12px;color:var(--mute)">
                <span>Zoradiť:</span>
                <a href="{{ $setSortUrl(null) }}" style="color:{{ !$activeSort ? 'var(--ink)' : 'inherit' }};text-decoration:{{ !$activeSort ? 'underline' : 'none' }};text-underline-offset:4px">Predvolené</a>
                <a href="{{ $setSortUrl('price-asc') }}" style="color:{{ $activeSort === 'price-asc' ? 'var(--ink)' : 'inherit' }};text-decoration:{{ $activeSort === 'price-asc' ? 'underline' : 'none' }};text-underline-offset:4px">Cena ↑</a>
                <a href="{{ $setSortUrl('price-desc') }}" style="color:{{ $activeSort === 'price-desc' ? 'var(--ink)' : 'inherit' }};text-decoration:{{ $activeSort === 'price-desc' ? 'underline' : 'none' }};text-underline-offset:4px">Cena ↓</a>
            </div>
        </div>
        @if($products->isEmpty())
            <div style="padding:64px 0;text-align:center;color:var(--mute)">
                <p>{{ $searchTerm ? 'Pre „' . $searchTerm . '“ sme nič nenašli. Skúste iný výraz alebo názov kolekcie.' : 'Pre zvolené filtre žiadne produkty.' }}</p>
                <a href="{{ route('shop.index') }}" style="color:inherit">Vyčistiť filtre →</a>
            </div>
        @else
            <div class="shop-grid">
                @foreach($products as $p)
                    @include('partials.product-card', ['p' => $p])
                @endforeach
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
(function () {
    var filToggle = document.querySelector('[data-fil-toggle]');
    if (filToggle) {
        filToggle.addEventListener('click', function () {
            var open = this.closest('.shop-fil').classList.toggle('open');
            this.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }
    document.querySelectorAll('[data-grp-toggle]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!window.matchMedia('(max-width: 1100px)').matches) return;
            var open = this.closest('.grp').classList.toggle('open');
            this.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    });
})();
</script>
@endpush

@endsection
