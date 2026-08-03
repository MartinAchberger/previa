@extends('layouts.app')

@section('title', 'Eshop - všetky línie · PREVIA')

@section('content')

@php
    $typeLabels = \App\Models\Product::typeLabels();

    $current = [
        'line' => $activeLine,
        'type' => $activeType,
        'sort' => $activeSort,
    ];
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
    $hasAnyFilter = (bool) ($activeLine || $activeType);
@endphp

<section class="shop-head">
    <div class="crumbs">
        <a href="{{ route('home') }}" style="color:inherit;text-decoration:none">PREVIA</a>
        <span class="sep">/</span>
        <span>Eshop</span>
    </div>
    <h1>Všetky<br><em>produkty.</em></h1>
    <div class="meta">
        <div class="ds">Profesionálna vlasová kozmetika z Talianska · až 97 % prírodných ingrediencií · vegan &amp; cruelty-free.</div>
        <a href="{{ route('quiz.show') }}" class="cnt" style="text-decoration:none;color:inherit;border:1px solid var(--line);padding:10px 18px">Nájdite rutinu pre svoje vlasy →</a>
    </div>
</section>

<div class="shop-body">
    <aside class="shop-fil">
        <div class="grp">
            <h4>Línia</h4>
            @foreach($lines as $line)
                <a href="{{ $toggleUrl('line', $line->slug) }}" class="opt {{ $activeLine === $line->slug ? 'on' : '' }}" style="text-decoration:none;color:inherit;display:flex">
                    <div class="box"></div>
                    <div class="lab">{{ $line->name }}</div>
                    <span class="cnt">{{ str_pad($line->products()->where('published', true)->count(), 2, '0', STR_PAD_LEFT) }}</span>
                </a>
            @endforeach
        </div>

        <div class="grp">
            <h4>Typ produktu</h4>
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
    </aside>

    <div class="shop-list">
        @if($activeLineModel && $activeLineModel->description && $lines->contains('slug', $activeLine))
            <div class="line-intro">
                <div class="line-intro-eyebrow">{{ $activeLineModel->eyebrow ?: 'Línia' }}</div>
                <h2>{{ $activeLineModel->name }}</h2>
                <p>{{ $activeLineModel->description }}</p>
            </div>
        @endif
        <div class="shop-list-bar">
            <div class="chips">
                @if($activeLine)
                    <a href="{{ $toggleUrl('line', $activeLine) }}" class="chip" style="text-decoration:none;color:inherit">{{ $lines->firstWhere('slug', $activeLine)?->name ?? $activeLine }} <span class="x">×</span></a>
                @endif
                @if($activeType)
                    <a href="{{ $toggleUrl('type', $activeType) }}" class="chip" style="text-decoration:none;color:inherit">{{ $typeLabels[$activeType] ?? $activeType }} <span class="x">×</span></a>
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
                <p>Pre zvolené filtre žiadne produkty.</p>
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

@endsection
