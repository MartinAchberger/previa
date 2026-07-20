@props(['p'])
@php
    $b2b = auth('b2b')->user();
    // Product sale first (applies to everyone), then B2B salon discount on top.
    $base = $p->salePrice();
    $effectivePrice = $b2b && $b2b->discount_pct > 0 ? round($base * (1 - $b2b->discount_pct / 100), 2) : $base;
    $effectiveNet = round($effectivePrice / (1 + \App\Models\Product::VAT_RATE), 2);
    $netPrimary = $b2b && !empty($b2b->vat_id);
    $showStrike = $effectivePrice < (float) $p->price;
    $cardHasShades = $p->hasShades();
    $payload = [
        'id'      => (string) $p->id,
        'code'    => $p->code,
        'name'    => $p->name,
        'line'    => $p->line_label,
        'volume'  => $p->volume,
        'price'   => $effectivePrice,
    ];
@endphp
<div class="card-wrap">
    <a href="{{ route('product.show', $p->slug) }}" class="card">
        <div class="ph">
            @if($p->image_url)
                <img src="{{ $p->image_url }}" alt="{{ $p->name }}" style="width:100%;height:100%;object-fit:contain;display:block;padding:12%;box-sizing:border-box">
            @else
                <div class="b-wrap">
                    <div>@include('partials.bottle', ['kind' => $p->kind, 'tone' => $p->tone, 'cap' => $p->cap ?: $p->tone, 'sub' => $p->complex, 'n' => $p->code, 'label' => 'PREVIA'])</div>
                </div>
            @endif
            @if($p->badge)
                <div class="badge">{{ $p->badge }}</div>
            @endif
            <div class="no">n° {{ $p->code }}</div>
        </div>
        <div class="line">{{ $p->line_label }}</div>
        <div class="name">{{ $p->name }}</div>
        <div class="pr">
            @if($netPrimary)
                <strong>€{{ number_format($effectiveNet, 2, ',', ' ') }}</strong>
            @elseif($showStrike)
                <strong>€{{ number_format($effectivePrice, 2, ',', ' ') }}</strong>
                <s>€{{ number_format($p->price, 2, ',', ' ') }}</s>
            @else
                <strong>{{ $p->price_formatted }}</strong>
            @endif
        </div>
    </a>
    @if ($cardHasShades)
        <a href="{{ route('product.show', $p->slug) }}" class="card-cta">
            <span>Vybrať odtieň</span><span aria-hidden="true">→</span>
        </a>
    @else
        <button type="button" class="card-cta" data-cart-add data-product="{{ json_encode($payload, JSON_UNESCAPED_UNICODE) }}">
            <span>Pridať do košíka</span><span aria-hidden="true">→</span>
        </button>
    @endif
</div>
