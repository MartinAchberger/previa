@extends('layouts.app')

@section('title', 'PREVIA PRO · ' . $b2b->salon_name)

@section('content')

@include('partials.b2b-nav', ['active' => 'pro'])

<section class="shop-head">
    <div class="crumbs">
        <a href="{{ route('b2b.dashboard') }}" style="color:inherit;text-decoration:none">Salón</a>
        <span class="sep">/</span>
        <span>Previa Pro</span>
    </div>
    <h1>PREVIA PRO<br><em>· všetko pre profesionála.</em></h1>
    <div class="meta">
        <div class="ds">Objav kompletnú profesionálnu ponuku PREVIA - od permanentných a bezamoniakových farieb cez zosvetľovacie produkty až po oxidanty a technickú starostlivosť pre každodennú prácu v salóne.</div>
        <nav class="pro-jump">
            @if ($colorProducts->isNotEmpty())<a href="#pro-farby">Farby</a>@endif
            @foreach ($proGroups as $key => $g)<a href="#pro-{{ $key }}">{{ $g['title'] }}</a>@endforeach
            @if ($salonSizes->isNotEmpty())<a href="#pro-balenia">Salónne balenia</a>@endif
        </nav>
    </div>
</section>

@if ($colorProducts->isNotEmpty())
    <section class="bulk-lines" id="pro-farby">
        <div class="section-head">
            <h2 class="h2">Farby <em>· celá paleta odtieňov.</em></h2>
        </div>
        <div class="grid-4">
            @foreach ($colorProducts as $cp)
                @php
                    $shadeCount = is_array($cp->shades) ? count($cp->shades) : 0;
                    $groups = $cp->shadesGrouped();
                    $groupKeys = array_keys($groups);
                    $groupCount = count($groupKeys);
                    $isLevelKeyed = $groupCount > 0 && is_int($groupKeys[0]);
                    $effPrice = $b2b && $b2b->discount_pct > 0
                        ? round($cp->price * (1 - $b2b->discount_pct / 100), 2)
                        : (float) $cp->price;
                    $previewShades = collect($cp->shades)->take(8);
                @endphp
                <a href="{{ route('product.show', $cp->slug) }}" class="bulk-line-card">
                    <div class="bulk-line-ph">
                        @if ($cp->image_url)
                            <img src="{{ $cp->image_url }}" alt="{{ $cp->name }}">
                        @else
                            <div class="bulk-line-bottle">
                                @include('partials.bottle', ['kind' => $cp->kind, 'tone' => $cp->tone, 'cap' => $cp->cap ?: $cp->tone, 'sub' => $cp->complex, 'n' => $cp->code, 'label' => 'PREVIA'])
                            </div>
                        @endif
                    </div>
                    <div class="bulk-line-meta">
                        <div class="line">{{ $cp->line?->name ?? $cp->line_label }}</div>
                        <div class="name">{{ $cp->name }}</div>
                        <div class="bulk-line-stats">
                            <span><strong>{{ $shadeCount }}</strong> odtieňov</span>
                            @if ($groupCount > 0)
                                <span class="dot">·</span>
                                @if ($isLevelKeyed)
                                    <span>úrovne {{ implode(', ', $groupKeys) }}</span>
                                @else
                                    <span><strong>{{ $groupCount }}</strong> rodín</span>
                                @endif
                            @endif
                            <span class="dot">·</span>
                            <span>{{ $cp->volume }}</span>
                        </div>
                        @if ($previewShades->isNotEmpty())
                            <div class="bulk-line-swatches">
                                @foreach ($previewShades as $shade)
                                    <span class="bulk-line-sw"
                                          @if(!empty($shade['color'])) style="background:{{ $shade['color'] }}" @endif
                                          title="{{ ($shade['code'] ?? '') . ' ' . ($shade['name'] ?? '') }}"></span>
                                @endforeach
                                @if ($shadeCount > $previewShades->count())
                                    <span class="bulk-line-sw-more">+{{ $shadeCount - $previewShades->count() }}</span>
                                @endif
                            </div>
                        @endif
                        <div class="bulk-line-cta">
                            <span class="pr">od €{{ number_format($effPrice, 2, ',', ' ') }}</span>
                            <span class="go">Otvoriť radu →</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>
@endif

@foreach ($proGroups as $key => $g)
    <section class="pro-group" id="pro-{{ $key }}">
        <div class="section-head">
            <h2 class="h2">{{ $g['title'] }} <em>· {{ $g['sub'] }}.</em></h2>
            <div class="section-sub">{{ $g['products']->count() }} {{ $g['products']->count() === 1 ? 'produkt' : ($g['products']->count() < 5 ? 'produkty' : 'produktov') }}</div>
        </div>
        <div class="grid-4">
            @foreach ($g['products'] as $p)
                @include('partials.product-card', ['p' => $p])
            @endforeach
        </div>
    </section>
@endforeach

@if ($salonSizes->isNotEmpty())
    <section class="pro-group" id="pro-balenia">
        <div class="section-head">
            <h2 class="h2">Salónne balenia <em>· litrové veľkosti.</em></h2>
            <div class="section-sub">{{ $salonSizes->count() }} produktov · menšie veľkosti nájdeš v eshope</div>
        </div>
        <div class="grid-4">
            @foreach ($salonSizes as $p)
                @include('partials.product-card', ['p' => $p])
            @endforeach
        </div>
    </section>
@endif

@endsection
