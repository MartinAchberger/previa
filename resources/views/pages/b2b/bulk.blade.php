@extends('layouts.app')

@section('title', 'Farby pre salóny · ' . $b2b->salon_name)

@section('content')

@include('partials.b2b-nav', ['active' => 'colors'])

<section class="shop-head">
    <div class="crumbs">
        <a href="{{ route('b2b.dashboard') }}" style="color:inherit;text-decoration:none">Salón</a>
        <span class="sep">/</span>
        <span>Farby</span>
    </div>
    <h1>Farby<br><em>· profesionálna paleta.</em></h1>
    <div class="meta">
        <div class="ds">Vyber radu farby a otvor detail s celou škálou odtieňov. V detaile si klikneš odtiene a množstvá naraz a jedným tlačidlom ich pridáš do košíka.</div>
    </div>
</section>

@if ($colorProducts->isEmpty())
    <div style="padding: 96px 56px; text-align: center; color: var(--mute)">
        <p>Zatiaľ žiadne farebné rady s odtieňmi.</p>
        <p style="font-size:13px">Pridaj odtiene cez admin v sekcii Produkty → vyber farebný produkt → Matrix „shades".</p>
    </div>
@else
    <section class="bulk-lines">
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

@endsection
