@extends('layouts.app')

@section('title', 'Tvoja rutina - PREVIA')

@section('content')

<section class="quiz-result">
    <div class="quiz-result-head">
        <div class="eyebrow">Diagnostika · Odporúčanie</div>
        <h1>Pre tvoje vlasy<br><em>{{ $line?->name ?? 'rutina' }}.</em></h1>
        @if ($line)
            <p class="lede">{{ $line->description }}</p>
        @endif
        <p class="quiz-rationale">{{ $rationale }}</p>
    </div>

    @if ($ritual->isNotEmpty())
        <div class="quiz-ritual">
            <div class="section-head" style="border-bottom:none;padding-bottom:0;margin-bottom:32px">
                <h2 class="h2">Tvoj <em>rituál.</em></h2>
                @php($rc = $ritual->count())
                <div class="section-sub">{{ $rc }} {{ $rc === 1 ? 'produkt' : ($rc < 5 ? 'produkty' : 'produktov') }} · línia {{ $line?->code }}</div>
            </div>
            <div class="grid-4">
                @foreach ($ritual as $p)
                    @include('partials.product-card', ['p' => $p])
                @endforeach
            </div>
        </div>
    @endif

    <div class="quiz-cta">
        <a href="{{ route('shop.index', ['line' => $line?->slug]) }}" class="btn">Pozrieť celú líniu →</a>
        <a href="{{ route('quiz.show') }}" class="btn btn-line">Spraviť diagnostiku znova</a>
    </div>
</section>

@endsection
