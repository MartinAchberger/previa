@php
    /** @var string $kind  tall|jar|sachet */
    /** @var string $tone  primary fill */
    /** @var string|null $cap  cap fill (defaults to $tone) */
    /** @var string $label brand label, default "ph" */
    /** @var string $sub   technical sub-label e.g. "HX-94" */
    /** @var string $n     numeric token e.g. "01" */
    $kind  ??= 'tall';
    $tone  ??= '#0b0b0a';
    $cap   ??= $tone;
    $label ??= 'PREVIA';
    $sub = ($sub ?? '') ?: 'Made in Italy';
    $n     ??= '01';
@endphp

@if($kind === 'jar')
    <svg viewBox="0 0 100 130" style="width:100%;height:100%">
        <rect x="20" y="14" width="60" height="14" fill="{{ $cap }}" rx="0.5" />
        <path d="M22 28 L78 28 L78 124 Q78 128 74 128 L26 128 Q22 128 22 124 Z" fill="{{ $tone }}" />
        <rect x="28" y="46" width="44" height="56" fill="#fbfbfa" opacity="0.96" />
        <text x="50" y="62" text-anchor="middle" font-family="Instrument Sans, sans-serif" font-size="6" font-weight="600" letter-spacing="1.2" fill="{{ $tone }}">{{ $label }}</text>
        <line x1="40" y1="68" x2="60" y2="68" stroke="{{ $tone }}" stroke-width="0.4" />
        <text x="50" y="80" text-anchor="middle" font-family="Inter, sans-serif" font-size="3" fill="{{ $tone }}" letter-spacing="0.6">{{ $sub }}</text>
    </svg>
@elseif($kind === 'sachet')
    <svg viewBox="0 0 100 140" style="width:100%;height:100%">
        <path d="M22 14 L78 14 L78 130 L22 130 Z" fill="{{ $tone }}" />
        <path d="M22 14 L78 14 L74 22 L26 22 Z" fill="{{ $cap }}" />
        <rect x="32" y="44" width="36" height="60" fill="#fbfbfa" opacity="0.94" />
        <text x="50" y="64" text-anchor="middle" font-family="Instrument Sans, sans-serif" font-size="6" font-weight="600" letter-spacing="1.2" fill="{{ $tone }}">{{ $label }}</text>
        <line x1="40" y1="71" x2="60" y2="71" stroke="{{ $tone }}" stroke-width="0.3" />
        <text x="50" y="84" text-anchor="middle" font-family="Inter, sans-serif" font-size="3" fill="{{ $tone }}" letter-spacing="0.6">{{ $sub }}</text>
    </svg>
@else
    <svg viewBox="0 0 100 170" style="width:100%;height:100%">
        <rect x="40" y="2" width="20" height="22" fill="{{ $cap }}" rx="0.5" />
        <path d="M28 28 L72 28 L74 162 Q74 166 70 166 L30 166 Q26 166 26 162 Z" fill="{{ $tone }}" />
        <rect x="32" y="58" width="36" height="78" fill="#fbfbfa" opacity="0.96" />
        <text x="50" y="76" text-anchor="middle" font-family="Instrument Sans, sans-serif" font-size="7" font-weight="600" letter-spacing="1.4" fill="{{ $tone }}">{{ $label }}</text>
        <line x1="38" y1="84" x2="62" y2="84" stroke="{{ $tone }}" stroke-width="0.4" />
        <text x="50" y="98" text-anchor="middle" font-family="Inter, sans-serif" font-size="3.2" fill="{{ $tone }}" letter-spacing="0.7">{{ $sub }}</text>
        <text x="50" y="120" text-anchor="middle" font-family="Inter, sans-serif" font-size="2.6" fill="{{ $tone }}" opacity="0.7" letter-spacing="0.4">250 ML</text>
    </svg>
@endif
